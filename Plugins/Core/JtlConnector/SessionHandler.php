<?php


namespace InnoShop\Plugins\Core\JtlConnector;


use Jtl\Connector\Core\Session\SessionHandlerInterface;

class SessionHandler implements SessionHandlerInterface
{
    private \PDO $conn;

    public function __construct(\PDO $conn)
    {
        $this->conn = $conn;
    }

    public function close()
    {
        return true;
    }

    public function destroy($session_id)
    {
        $stmt = $this->conn->prepare("DELETE FROM jtl_connector_session WHERE id = :id");
        $stmt->execute(['id' => $session_id]);
    }

    public function gc($maxlifetime)
    {
        $stmt = $this->conn->prepare("
            DELETE FROM jtl_connector_session
            WHERE timestamp < NOW() - INTERVAL(:maxlifetime second)
        ");
        $stmt->execute(['maxlifetime' => $maxlifetime]);
    }

    public function open($save_path, $name)
    {
        return true;
    }

    public function read($session_id)
    {
        $stmt = $this->conn->prepare("
            SELECT data
            FROM jtl_connector_session
            WHERE id = :id        
        ");
        $stmt->execute(['id' => $session_id]);
    }

    public function write($session_id, $session_data)
    {
        $stmt = $this->conn->prepare("
            INSERT INTO jtl_connector_session(id, timestamp, data)
            VALUES (:id, NOW(), :data)
            ON CONFLICT (jtl_connector_session_pkey)
            DO
                UPDATE SET data = :data
        ");
        $stmt->execute(['id' => $session_id, 'data' => $session_data]);
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