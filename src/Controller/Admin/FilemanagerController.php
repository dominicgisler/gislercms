<?php

namespace GislerCMS\Controller\Admin;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
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
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(Request $request, Response $response): Response
    {
        return $this->render($request, $response, 'admin/filemanager.twig');
    }
}
