<?php


namespace InnoShop\Kernel;


final class DatabaseConnectionData
{
    private string $host;
    private string $user;
    private string $pass;
    private int $port;
    /** @var callable */
    private $postInit;

    public function __construct(string $host, string $user, string $pass, int $port = 5432, callable $postInit = null)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pass = $pass;
        $this->port = $port;
        $this->postInit = $postInit ?: fn() => null;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getPass(): string
    {
        return $this->pass;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getPostInit(): callable
    {
        return $this->postInit;
    }
}