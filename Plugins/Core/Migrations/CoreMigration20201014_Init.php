<?php


namespace InnoShop\Plugins\Core\Migrations;


use InnoShop\Kernel\MigrationInterface;
use PDO;

class CoreMigration20201014_Init implements MigrationInterface
{
    private PDO $conn;

    /**
     * CoreMigration20201014_Init constructor.
     * @param PDO $conn
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    function getId(): string
    {
        return '20201014_Init';
    }

    function getDescription(): string
    {
        return 'Installes a test table';
    }

    function migrate(): void
    {
        $this->conn->exec("
            CREATE TABLE jtl_connector_session (
                id VARCHAR PRIMARY KEY,
                timestamp TIMESTAMP,
                data TEXT               
            );

            CREATE TABLE product (
                id INT PRIMARY KEY,
                data JSONB NOT NULL
            );
            CREATE TABLE category (
                id INT PRIMARY KEY,
                data JSONB NOT NULL
            );
            CREATE TABLE manufacturer (
                id INT PRIMARY KEY,
                data JSONB NOT NULL
            );
        ");
    }

}