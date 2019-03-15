<?php

namespace GislerCMS\Controller;

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
        return $this->render($request, $response, 'admin/index.twig');
    }
}
