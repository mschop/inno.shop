<?php

namespace InnoShopExample;

use InnoShop\Kernel\DatabaseConnectionData;
use InnoShop\Kernel\Kernel;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require __DIR__ . '/../vendor/autoload.php';

function getKernel(): Kernel
{
    $consoleHandler = new StreamHandler('php://stdout');
    $logger = new Logger('inno.shop');
    $logger->pushHandler($consoleHandler);
    $kernel = new Kernel(
        new DatabaseConnectionData('postgresql', 'postgres', 'postgres'),
        $logger
    );
    return $kernel;
}

