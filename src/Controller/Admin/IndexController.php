<?php

namespace GislerCMS\Controller\Admin;

use GislerCMS\Model\Client;
use GislerCMS\Model\Page;
use GislerCMS\Model\Session;
use GislerCMS\Model\Visit;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class IndexController
 * @package GislerCMS\Controller\Admin
 */
class IndexController extends AbstractController
{
    const NAME = 'admin-index';
    const PATTERN = '{admin_route}[/]';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        return $this->render($request, $response, 'admin/index.twig', [
            'clients' => Client::getAll(),
            'sessions' => Session::getAll(),
            'visits' => Visit::getAll(),
            'pages' => Page::getAll()
        ]);
    }
}
