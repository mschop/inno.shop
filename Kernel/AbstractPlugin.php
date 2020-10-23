<?php


namespace InnoShop\Kernel;


use Psr\Container\ContainerInterface;
use Slim\App;

abstract class AbstractPlugin
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function initializeWeb(App $app): void
    {

    }

    public function getTemplateDirs(): array
    {

    }
}