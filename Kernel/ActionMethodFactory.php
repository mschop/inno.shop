<?php


namespace InnoShop\Kernel;


use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

final class ActionMethodFactory
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create(string $serviceId): callable
    {
        $container = $this->container;
        return function(Request $request, Response $response) use ($serviceId, $container) {
            $service = $container->get($serviceId);
            if (!$service instanceof ActionInterface) {
                throw new \Exception("Service $serviceId does not implement Interface " . ActionInterface::class);
            }
            return $service->handle($request, $response);
        };
    }
}