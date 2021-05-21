<?php

namespace GislerCMS\Controller\Admin\Misc;

use GislerCMS\Controller\Admin\AbstractController;
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
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        return $this->render($request, $response, 'admin/misc/license.twig');
    }
}
