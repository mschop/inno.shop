<?php


namespace InnoShop\Plugins\Core;


use Eloquent\Pathogen\Path;
use InnoShop\Kernel\AbstractPlugin;
use InnoShop\Kernel\ActionMethodFactory;
use InnoShop\Kernel\Container;
use InnoShop\Plugins\Core\Actions\IndexAction;
use InnoShop\Plugins\Core\Actions\JtlConnectorAction;
use InnoShop\Plugins\Core\JtlConnector\Connector;
use InnoShop\Plugins\Core\JtlConnector\Preparer;
use InnoShop\Plugins\Core\JtlConnector\PreparerInterface;
use InnoShop\Plugins\Core\JtlConnector\PrimaryKeyMapper;
use InnoShop\Plugins\Core\JtlConnector\SessionHandler;
use InnoShop\Plugins\Core\JtlConnector\TokenValidator;
use InnoShop\Plugins\Core\Migrations\CoreMigration20201014_Init;
use Jtl\Connector\Core\Application\Application;
use Jtl\Connector\Core\Authentication\TokenValidatorInterface;
use Jtl\Connector\Core\Connector\ConnectorInterface;
use Jtl\Connector\Core\Mapper\PrimaryKeyMapperInterface;
use Jtl\Connector\Core\Session\SessionHandlerInterface;
use NoTee\TemplateInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Slim\App;

class CorePlugin extends AbstractPlugin
{
    /**
     * CoreAbstractPlugin constructor.
     * @param Container $container
     */
    public function __construct(Container  $container)
    {
        parent::__construct($container);
        $this->registerServices();
        $this->registerActions();
        $this->registerMigrations();
    }

    public function initializeWeb(App $app): void
    {
        $factory = $this->container->get(ActionMethodFactory::class);
        assert($factory instanceof ActionMethodFactory);
        $app->any('/', $factory->create(IndexAction::class));
        $app->any('/jtlconnector', $factory->create(JtlConnectorAction::class));
    }

    public function getTemplateDirs(): array
    {
        return [__DIR__ . '/Templates'];
    }

    private function registerServices(): void
    {
        $this->container->add(PrimaryKeyMapperInterface::class, fn() => new PrimaryKeyMapper());
        $this->container->add(ConnectorInterface::class, fn(Container $c) => new Connector(
            $c->get(PrimaryKeyMapperInterface::class),
            $c->get(TokenValidatorInterface::class),
        ));
        $this->container->add(TokenValidatorInterface::class, fn(Container $c) => new TokenValidator(
            $c->get('config')['jtl_connector']['token'],
        ));
        $this->container->add(Application::class, function(Container $c) {
            $c->get(PreparerInterface::class)->prepare();
            $application = new Application(
                $c->get(ConnectorInterface::class),
                $c->get('config')['jtl_connector']['path'],
            );
            $application->setSessionHandler(new SessionHandler($c->get('db_connection')));
            return $application;
        });
        $this->container->add(PreparerInterface::class, fn(Container $c) => new Preparer(
            Path::fromString($c->get('config')['root']),
            $c->get(LoggerInterface::class),
        ));
        $this->container->add(SessionHandlerInterface::class, fn(Container $c) => new SessionHandler(
            $c->get('db_connection'),
        ));
    }

    private function registerActions(): void
    {
        $this->container->add(
            IndexAction::class,
            fn(Container $c) => new IndexAction($c->get(TemplateInterface::class)),
        );
        $this->container->add(
            JtlConnectorAction::class,
            fn(Container $c) => new JtlConnectorAction($c->get(Application::class))
        );
    }

    private function registerMigrations()
    {
        $this->container->add(
            CoreMigration20201014_Init::class,
            fn(Container $c) => new CoreMigration20201014_Init($c->get('db_connection')),
            ['migration']
        );
    }
}