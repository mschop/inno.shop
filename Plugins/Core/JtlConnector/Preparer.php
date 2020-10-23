<?php


namespace InnoShop\Plugins\Core\JtlConnector;


use Exception;
use Eloquent\Pathogen\PathInterface;
use Psr\Log\LoggerInterface;

class Preparer implements PreparerInterface
{
    protected const DEFAULT_CONFIG = [
        'debug' => false,
    ];

    protected PathInterface $path;
    protected LoggerInterface $logger;

    function __construct(PathInterface $path, LoggerInterface $logger)
    {
        $this->path = $path->joinAtoms('jtl_connector');
        $this->logger = $logger;
    }

    /**
     * @throws Exception
     */
    function prepare(): void
    {
        $permsMsg = "Please check permissions of the directory.";

        if (!is_dir($this->path)) {
            if(mkdir($this->path)) {
                $this->logger->info("Created jtl connector dir under {$this->path}");
            } else {
                $msg = "JTL connector dir does not exist and an error occurred on creating it. $permsMsg";
                $this->logger->error($msg);
                throw new Exception($msg);
            }
        }

        $configDir = $this->path->joinAtoms('config');

        if (!is_dir($configDir)) {
            if (mkdir($configDir)) {
                $this->logger->info("Created jtl config dir");
            } else {
                $msg = "JTL connector config dir does not exist and an error occurered on creating it. $permsMsg";
                $this->logger->error($msg);
                throw new Exception($msg);
            }
        }

        $configFile = $configDir->joinAtoms('config.json');
        if (!is_file($configFile)) {
            $json = json_encode(static::DEFAULT_CONFIG, JSON_THROW_ON_ERROR|JSON_PRETTY_PRINT);
            if (file_put_contents($configFile, $json) === false) {
                $msg = "The jtl connector config file does not exist and cannot be created. $permsMsg";
                $this->logger->error($msg);
                throw new Exception($msg);
            } else {
                $this->logger->info("JTL-Connector config file created");
            }
        }
    }
}