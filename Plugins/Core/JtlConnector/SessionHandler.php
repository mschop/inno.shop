<?php


namespace InnoShop\Plugins\Core\JtlConnector;


use Jtl\Connector\Core\Session\SessionHandlerInterface;
use Psr\Log\LoggerInterface;

class SessionHandler implements SessionHandlerInterface
{
    protected \PDO $conn;
    protected LoggerInterface $logger;

    public function __construct(\PDO $conn, LoggerInterface $logger)
    {
        $this->conn = $conn;
        $this->logger = $logger;
    }

    public function close()
    {
        $this->logger->debug('Session by jtl_connector opened');

        return true;
    }

    public function destroy($session_id)
    {
        $this->logger->debug('Remove jtl_connector session', ['id' => $session_id]);

        $stmt = $this->conn->prepare("DELETE FROM jtl_connector_session WHERE id = :id");
        $stmt->execute(['id' => $session_id]);
    }

    public function gc($maxlifetime)
    {
        $this->logger->debug('Executing jtl_connector session gc', ['maxlifetime' => $maxlifetime]);

        $stmt = $this->conn->prepare("
            DELETE FROM jtl_connector_session
            WHERE timestamp < NOW() - INTERVAL :interval
        ");
        $stmt->execute(['interval' => "$maxlifetime second"]);
    }

    public function open($save_path, $name)
    {
        $this->logger->debug('Opening jtl_connector session', ['save_path' => $save_path, 'name' => $name]);

        return true;
    }

    public function read($session_id)
    {
        $this->logger->debug('Reading jtl_connector session', ['id' => $session_id]);

        $stmt = $this->conn->prepare("
            SELECT data
            FROM jtl_connector_session
            WHERE id = :id        
        ");
        $stmt->execute(['id' => $session_id]);
        return $stmt->fetchColumn();
    }

    public function write($session_id, $session_data)
    {
        $this->logger->debug("Write jtl_connector session", ['id' => $session_id, 'data' => $session_data]);

        $stmt = $this->conn->prepare("
            INSERT INTO jtl_connector_session(id, timestamp, data)
            VALUES (:id, NOW(), :data1)
            ON CONFLICT (id)
            DO
                UPDATE SET data = :data2
        ");
        $stmt->execute(['id' => $session_id, 'data1' => $session_data, 'data2' => $session_data]);
    }

    public function validateId($session_id)
    {
        $stmt = $this->conn->prepare("
            SELECT COUNT(*)
            FROM jtl_connector_session
            WHERE id = :id
        ");
        $stmt->execute(['id' => $session_id]);
        return $stmt->fetchColumn(0) !== 0;
    }

    public function updateTimestamp($session_id, $session_data)
    {
        $stmt = $this->conn->prepare("
            UPDATE jtl_connector_session
            SET
                timestamp = NOW(),
                data = :data
            WHERE id = :id
        ");
        $stmt->execute(['id' => $session_id, 'data' => $session_data]);
    }
}