<?php

namespace InnoShopExample;

use InnoShop\Kernel\Kernel;
use InnoShop\Plugins\Core\CorePlugin;

require __DIR__ . '/../vendor/autoload.php';

function getKernel(): Kernel
{
    return new Kernel([
        'root' => __DIR__,
        'db' => [
            'host' => 'postgresql',
            'port' => 5432,
            'user' => 'postgres',
            'pass' => 'postgres',
        ],
        'jtl_connector' => [
            'token' => 'testtoken',
        ],
    ]);
}

