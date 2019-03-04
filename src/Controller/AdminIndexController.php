<?php

namespace GislerCMS\Controller;

use GislerCMS\Model\DbModel;
use GislerCMS\Model\Page;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminIndexController
 * @package GislerCMS\Controller
 */
class AdminIndexController extends AbstractController
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
        DbModel::init($this->get('pdo'));
        $pages = Page::getAll();

        return $this->render($request, $response, 'admin/index.twig', [
            'pages' => $pages
        ]);
    }
}
