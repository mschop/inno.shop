<?php

namespace InnoShopExample;

require __DIR__ . '/../bootstrap.php';

$kernel = getKernel();
$kernel->handleRequest();