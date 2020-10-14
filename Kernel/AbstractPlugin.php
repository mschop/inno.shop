<?php


namespace InnoShop\Kernel;


use Psr\Container\ContainerInterface;

abstract class AbstractPlugin
{
    protected Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }
}