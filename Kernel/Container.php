<?php

namespace InnoShop\Kernel;


use Psr\Container\ContainerInterface;

final class Container implements ContainerInterface
{
    private array $services = [];
    private array $tags = [];

    public function get($id)
    {
        if (!isset($this->services[$id])) new ServiceNotFoundException("Service '$id' not found.");
        return $this->services[$id]($this);
    }

    public function getByTag(string $tag): array
    {
        return array_map(
            fn($id) => $this->get($id),
            $this->tags[$tag] ?? []
        );
    }

    public function has($id)
    {
        return isset($this->services[$id]);
    }

    public function registerSingleton(string $id, callable $callable, array $tags = []): void
    {
        $this->services[$id] = function ($container) use ($callable) {
            static $result;
            if (empty($result)) {
                $result = $callable($container);
            }
            return $result;
        };
        $this->registerTags($id, $tags);
    }

    public function registerFactory(string $id, callable $callable, array $tags = []): void
    {
        $this->services[$id] = $callable;
        $this->registerTags($id, $tags);
    }

    private function registerTags(string $serviceId, array $tags = [])
    {
        foreach ($tags as $tag) {
            if (!isset($this->tags[$tag])) {
                $this->tags[$tag] = [$serviceId];
            } else {
                $this->tags[$tag][] = $serviceId;
            }
        }
    }
}
