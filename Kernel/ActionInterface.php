<?php


namespace InnoShop\Kernel;


use Slim\Psr7\Request;
use Slim\Psr7\Response;

interface ActionInterface
{
    function handle(Request $request, Response $response): Response;
}