<?php

namespace GislerCMS\Controller;

use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class RobotsController
 * @package GislerCMS\Controller
 */
class RobotsController extends AbstractController
{
    const NAME = 'robots-txt';
    const PATTERN = '/robots.txt';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        return $this->render(
            $request,
            $response->withAddedHeader('Content-Type', 'text/plain'),
            'robots.twig'
        );
    }
}
