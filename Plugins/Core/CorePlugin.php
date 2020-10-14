<?php


namespace InnoShop\Plugins\Core;


use InnoShop\Kernel\AbstractPlugin;
use InnoShop\Kernel\Container;
use InnoShop\Plugins\Core\Migrations\CoreMigration20201014_Init;

class CorePlugin extends AbstractPlugin
{
    /**
     * CoreAbstractPlugin constructor.
     * @param Container $container
     */
    public function __construct(Container  $container)
    {
        parent::__construct($container);
        $this->addMigrations();
    }

    private function addMigrations()
    {
        $this->container->registerSingleton(
            CoreMigration20201014_Init::class,
            fn(Container $c) => new CoreMigration20201014_Init($c->get('db_connection')),
            ['migration']
        );
    }
}