<?php

namespace GislerCMS\Controller\Admin\Auth;

use GislerCMS\Controller\Admin\AdminAbstractController;
use GislerCMS\Helper\SessionHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminLogoutController
 * @package GislerCMS\Controller
 */
class AdminLogoutController extends AdminAbstractController
{
    const NAME = 'admin-logout';
    const PATTERN = '{admin_route}/logout';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke($request, $response)
    {
        $cont = SessionHelper::getContainer();
        $cont->offsetUnset('user');

        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route']);
    }
}
