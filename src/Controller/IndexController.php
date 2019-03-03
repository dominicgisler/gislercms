<?php

namespace GislerCMS\Controller;

use GislerCMS\Model\DbModel;
use GislerCMS\Model\Page;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class IndexController
 * @package GislerCMS\Controller
 */
class IndexController extends AbstractController
{
    const NAME = 'index';
    const PATTERN = '/';
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

        return $this->render($request, $response, 'index.twig', [
            'pages' => $pages
        ]);
    }
}
