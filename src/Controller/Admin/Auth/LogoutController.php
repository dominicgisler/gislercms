<?php

namespace GislerCMS\Controller\Admin\Auth;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Helper\SessionHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LogoutController
 * @package GislerCMS\Controller\Admin\Auth
 */
class LogoutController extends AbstractController
{
    const NAME = 'admin-logout';
    const PATTERN = '{admin_route}/logout';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $cont = SessionHelper::getContainer();
        $cont->offsetUnset('user');

        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route']);
    }
}
