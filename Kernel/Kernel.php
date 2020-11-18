<?php


namespace InnoShop\Kernel;

use Eloquent\Pathogen\PathInterface;
use NoTee\NoTee;
use NoTee\NoTeeInterface;
use PDO;
use Eloquent\Pathogen\Path;
use Eloquent\Pathogen\RelativePath;
use InnoShop\Kernel\Commands\InstallCommand;
use InnoShop\Kernel\Db\DatabaseTruncate;
use InnoShop\Kernel\Db\DatabaseTruncateInterface;
use InnoShop\Kernel\Db\IsDatabaseEmptyCheck;
use InnoShop\Kernel\Db\IsDatabaseEmptyCheckInterface;
use InnoShop\Plugins\Core\CorePlugin;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Symfony\Component\Console\Application;


/**
 * Class Container
 * @package InnoShop
 */
final class Kernel
{
    private array $config;
    private Container $container;
    /** @var AbstractPlugin[] */
    private array $plugins = [];

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->container = new Container();
        $this->plugins[] = new CorePlugin($this->container);
    }

    public function addPlugin(string $class): void
    {
        $plugin = new $class($this->container);
        if (!$plugin instanceof AbstractPlugin) {
            throw new \Exception("Class $class is not an instance of " . AbstractPlugin::class);
        }
        $this->plugins[] = $plugin;
    }

    public function handleRequest(Request $request = null)
    {
        $this->boot();
        AppFactory::setContainer($this->container);
        $app = AppFactory::create();
        $app->addRoutingMiddleware();
        $app->addErrorMiddleware(true, true, true, $this->container->get(LoggerInterface::class));
        foreach ($this->plugins as $plugin) {
            $plugin->initializeWeb($app);
        }
        $app->run($request);
    }

    public function handleCli()
    {
        $this->boot();
        $commands = $this->container->getByTag('cli_command');
        $app = new Application();
        foreach ($commands as $command) {
            $app->add($command);
        }
        $app->run();
    }

    private function boot()
    {
        $this->setupContainer();
    }

    private function setupContainer(): void
    {
        $this->container->add('config', fn() => $this->config);

        $this->container->add('path_root', fn(Container $c) => Path::fromString($c->get('config')['root']));
        $this->container->add('path_var', fn(Container $c) => $c->get('path_root')->joinAtoms('var'));

        $this->container->add(LoggerInterface::class, function(Container $c) {
            $logLevel = $c->get('config')['log_level'] ?? Logger::ERROR;
            $path = $c->get('path_var');
            assert($path instanceof PathInterface);
            $path = $path->join(new RelativePath(['log', date('Y-m-d') . '.log']));
            $consoleHandler = new StreamHandler((string)$path, $logLevel);
            $logger = new Logger('inno.shop');
            $logger->pushHandler($consoleHandler);
            return $logger;
        });

        $this->container->add('db_connection', function() {
            $c = $this->config['db'];
            $dsn = "pgsql:host={$c['host']};port={$c['port']}";
            $pdo = new PDO($dsn, $c['user'], $c['pass']);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $pdo;
        });

        $this->container->add(ActionMethodFactory::class, fn(Container $c) => new ActionMethodFactory($c));

        $this->container->add(Migrator::class, fn(Container $c) => new Migrator(
            $c->get('db_connection'),
            $c->getByTag('migration'),
        ));

        $this->container->add(IsDatabaseEmptyCheckInterface::class, fn(Container $c) => new IsDatabaseEmptyCheck(
            $c->get('db_connection'),
        ));

        $this->container->add(DatabaseTruncateInterface::class, fn(Container $c) => new DatabaseTruncate(
            $c->get('db_connection'),
        ));

        $this->container->add(
            InstallCommand::class,
            fn(Container $c) => new InstallCommand(
                $c->get(Migrator::class),
                $c->get(IsDatabaseEmptyCheckInterface::class),
                $c->get(DatabaseTruncateInterface::class),
            ),
            ['cli_command'],
        );

        $this->container->add(NoTeeInterface::class, function (Container $c): NoTeeInterface {
            $templateDirs = array_reduce(
                $this->plugins,
                fn (array $carry, AbstractPlugin $item) => array_merge($carry, $item->getTemplateDirs()),
                [],
            );
            $noTee = NoTee::create('utf-8', $templateDirs, [], false);
            $noTee->enableGlobal();

            return $noTee;
        });
    }
}