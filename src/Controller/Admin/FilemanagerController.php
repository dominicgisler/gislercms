<?php

namespace GislerCMS\Controller\Admin;

use Exception;
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
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->render($request, $response, 'admin/filemanager.twig');
    }
}
