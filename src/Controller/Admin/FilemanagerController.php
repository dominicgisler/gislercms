<?php

namespace GislerCMS\Controller\Admin;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class FilemanagerController
 * @package GislerCMS\Controller\Admin
 */
class FilemanagerController extends AbstractController
{
    const NAME = 'admin-filemanager';
    const PATTERN = '{admin_route}/filemanager';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        return $this->render($request, $response, 'admin/filemanager.twig');
    }
}
