<?php


namespace InnoShop\Plugins\Core\JtlConnector;


use Jtl\Connector\Core\Mapper\PrimaryKeyMapperInterface;

class PrimaryKeyMapper implements PrimaryKeyMapperInterface
{
    public function getHostId(int $type, string $endpointId): ?int
    {
        return $endpointId;
    }

    public function getEndpointId(int $type, int $hostId): ?string
    {
        return $hostId;
    }

    public function save(int $type, string $endpointId, int $hostId): bool
    {
        return true;
    }

    public function delete(int $type, string $endpointId = null, int $hostId = null): bool
    {
        return true;
    }

    public function clear(int $type = null): bool
    {
        return true;
    }
}