<?php

namespace GislerCMS\Controller\Admin\Stats;

use GislerCMS\Controller\Admin\AbstractController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ClientsController
 * @package GislerCMS\Controller\Admin
 */
class ClientsController extends AbstractController
{
    const NAME = 'admin-stats-clients';
    const PATTERN = '{admin_route}/stats/clients[/{option}]';
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
        $opt = $request->getAttribute('route')->getArgument('option');

        return $this->render($request, $response, 'admin/stats/clients.twig', [
            'opt' => $opt
        ]);
    }
}
