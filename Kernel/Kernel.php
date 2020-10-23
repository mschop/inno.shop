<?php


namespace InnoShop\Kernel;

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
use NoTee\BlockManager;
use NoTee\DefaultEscaper;
use NoTee\NodeFactory;
use NoTee\Template;
use NoTee\TemplateInterface;
use NoTee\UriValidator;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
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

        $this->container->add(LoggerInterface::class, function(Container $c) {
            $path = Path::fromString($c->get('config')['root'])->join(new RelativePath(['var', 'log', 'prod.log']));
            $consoleHandler = new StreamHandler($path);
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

        $this->container->add(LoggerInterface::class, fn() => new NullLogger()); // TODO create real logger

        $this->container->add(
            InstallCommand::class,
            fn(Container $c) => new InstallCommand(
                $c->get(Migrator::class),
                $c->get(IsDatabaseEmptyCheckInterface::class),
                $c->get(DatabaseTruncateInterface::class),
            ),
            ['cli_command'],
        );

        $this->container->add(TemplateInterface::class, function (Container $c): TemplateInterface {
            global $noTee;

            $noTee = new NodeFactory(
                new DefaultEscaper('utf-8'),
                new UriValidator(),
                new BlockManager(),
                true //todo
            );

            require __DIR__ . '/../vendor/mschop/notee/global.php';

            $template = new Template(
                array_reduce(
                    $this->plugins,
                    fn (array $carry, AbstractPlugin $item) => array_merge($carry, $item->getTemplateDirs()),
                    []
                ),
                $noTee
            );
            $noTee->setTemplate($template);

            return $template;
        });


    }
}