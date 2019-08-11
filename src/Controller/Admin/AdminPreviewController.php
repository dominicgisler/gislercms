<?php

namespace GislerCMS\Controller\Admin;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminPreviewController
 * @package GislerCMS\Controller
 */
class AdminPreviewController extends AdminAbstractController
{
    const NAME = 'admin-preview';
    const PATTERN = '{admin_route}/preview';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        return $this->render($request, $response, 'admin/preview.twig');
    }
}
