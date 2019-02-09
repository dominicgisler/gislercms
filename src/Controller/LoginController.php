<?php

namespace GislerCMS\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class LoginController
 * @package GislerCMS\Controller
 */
class LoginController extends AbstractController
{
    const NAME = 'login';
    const PATTERN = '/login';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function __invoke($request, $response)
    {
        return $this->get('view')->render($response, 'login.twig');
    }
}