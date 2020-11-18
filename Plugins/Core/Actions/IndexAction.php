<?php


namespace InnoShop\Plugins\Core\Actions;


use InnoShop\Kernel\ActionInterface;
use NoTee\NoTee;
use NoTee\NoTeeInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class IndexAction implements ActionInterface
{
    protected NoTeeInterface $noTee;

    public function __construct(NoTee $noTee)
    {
        $this->noTee = $noTee;
    }

    function handle(Request $request, Response $response): Response
    {
        $dom = $this->noTee->render('index.html.php', [
            'products' => [
                'Lacoste Shirt XL Blau',
                'Dracul Mode Hose M',
                'Adidas Short',
            ],
        ]);
        $response->getBody()->write((string)$dom);
        return $response;
    }
}