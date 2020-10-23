<?php


namespace InnoShop\Plugins\Core\Actions;


use InnoShop\Kernel\ActionInterface;
use Jtl\Connector\Core\Application\Application;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class JtlConnectorAction implements ActionInterface
{
    protected Application $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    function handle(Request $request, Response $response): Response
    {
        $this->application->run();
        return $response;
    }
}