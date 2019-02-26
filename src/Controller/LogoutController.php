<?php

namespace GislerCMS\Controller;

use GislerCMS\Entity\SessionHelper;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LogoutController
 * @package GislerCMS\Controller
 */
class LogoutController extends AbstractController
{
    const NAME = 'logout';
    const PATTERN = '/logout';
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

        return $response->withRedirect($this->get('base_url'));
    }
}
