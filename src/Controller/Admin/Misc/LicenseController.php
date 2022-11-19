<?php

namespace GislerCMS\Controller\Admin\Misc;

use GislerCMS\Controller\Admin\AbstractController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LicenseController
 * @package GislerCMS\Controller\Admin\Misc
 */
class LicenseController extends AbstractController
{
    const NAME = 'admin-misc-license';
    const PATTERN = '{admin_route}/misc/license';
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
        return $this->render($request, $response, 'admin/misc/license.twig');
    }
}
