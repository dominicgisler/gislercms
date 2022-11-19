<?php

namespace GislerCMS\Controller\Admin;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PreviewController
 * @package GislerCMS\Controller\Admin
 */
class PreviewController extends AbstractController
{
    const NAME = 'admin-preview';
    const PATTERN = '{admin_route}/preview';
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
        return $this->render($request, $response, 'admin/preview.twig');
    }
}
