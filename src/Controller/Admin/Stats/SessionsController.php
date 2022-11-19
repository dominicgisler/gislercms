<?php

namespace GislerCMS\Controller\Admin\Stats;

use GislerCMS\Controller\Admin\AbstractController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SessionsController
 * @package GislerCMS\Controller\Admin
 */
class SessionsController extends AbstractController
{
    const NAME = 'admin-stats-sessions';
    const PATTERN = '{admin_route}/stats/sessions[/{id:[0-9]+}]';
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
        $id = (int) $request->getAttribute('route')->getArgument('id');

        return $this->render($request, $response, 'admin/stats/sessions.twig', [
            'id' => $id
        ]);
    }
}
