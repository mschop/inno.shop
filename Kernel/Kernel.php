<?php


namespace InnoShop\Kernel;

use InnoShop\Kernel\Commands\InstallCommand;
use InnoShop\Plugins\Core\CorePlugin;
use Psr\Log\LoggerInterface;
use PDO;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Symfony\Component\Console\Application;

/**
 * Class Container
 * @package InnoShop
 */
final class Kernel
{
    private DatabaseConnectionData $databaseConnectionData;
    private LoggerInterface $logger;
    private Container $container;
    /** @var string[] */
    private array $plugins = [
        CorePlugin::class,
    ];

    public function __construct(DatabaseConnectionData $databaseConnectionData, LoggerInterface $logger)
    {
        $this->databaseConnectionData = $databaseConnectionData;
        $this->logger = $logger;
        $this->container = new Container();
    }

    public function addPlugin(string $bootstrapClass): void
    {
        $this->plugins[] = $bootstrapClass;
    }

    public function handleRequest(Request $request = null)
    {
        $this->boot();
        AppFactory::setContainer($this->container);
        $app = AppFactory::create();
        $app->addRoutingMiddleware();
        $app->addErrorMiddleware(true, true, true, $this->logger);
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
        $this->loadPlugins();
    }

    private function loadPlugins()
    {
        foreach ($this->plugins as $plugin) {
            new $plugin($this->container);
        }
    }

    private function setupContainer(): void
    {
        $this->container->registerSingleton('db_connection', function() {
            $d = $this->databaseConnectionData;
            $dsn = "pgsql:host={$d->getHost()};port={$d->getPort()}";
            $pdo = new PDO($dsn, $d->getUser(), $d->getPass());
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $d->getPostInit()($pdo);
            return $pdo;
        });

        $this->container->registerSingleton('plugins', function() {
            return array_map(
                fn(string $plugin) => new $plugin($this->container),
                $this->plugins,
            );
        });

        $this->container->registerSingleton(Migrator::class, fn(Container $c) => new Migrator(
            $c->get('db_connection'),
            $c->getByTag('migration'),
        ));

        $this->container->registerSingleton(
            InstallCommand::class,
            fn(Container $c) => new InstallCommand($c->get(Migrator::class)),
            ['cli_command'],
        );
    }
}