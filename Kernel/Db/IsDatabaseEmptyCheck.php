<?php


namespace InnoShop\Kernel\Db;


use PDO;

class IsDatabaseEmptyCheck implements IsDatabaseEmptyCheckInterface
{
    private PDO $conn;

    /**
     * IsEmptyCheck constructor.
     * @param PDO $conn
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    function isEmpty(): bool
    {
        return $this->conn->query("
            SELECT COUNT(*)
            FROM pg_catalog.pg_tables
            WHERE schemaname != current_database()
        ")->fetchColumn(0) === 0;
    }
}