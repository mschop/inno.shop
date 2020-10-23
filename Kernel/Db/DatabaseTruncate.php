<?php


namespace InnoShop\Kernel\Db;


use PDO;

class DatabaseTruncate implements DatabaseTruncateInterface
{
    protected PDO $conn;

    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    function apply(): void
    {
        $this->conn->exec("
            DROP SCHEMA public CASCADE;
            CREATE SCHEMA public;        
        ");
    }
}