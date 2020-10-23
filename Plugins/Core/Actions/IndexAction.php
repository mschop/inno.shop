<?php


namespace InnoShop\Plugins\Core\Actions;


use InnoShop\Kernel\ActionInterface;
use NoTee\TemplateInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;

class IndexAction implements ActionInterface
{
    protected TemplateInterface $template;

    public function __construct(TemplateInterface $template)
    {
        $this->template = $template;
    }

    function handle(Request $request, Response $response): Response
    {
        $dom = $this->template->render('index.html.php', [
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