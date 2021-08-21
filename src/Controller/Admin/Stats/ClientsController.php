<?php

namespace GislerCMS\Controller\Admin\Stats;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Client;
use GislerCMS\Model\Session;
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
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $opt = $request->getAttribute('route')->getArgument('option');

        return $this->render($request, $response, 'admin/stats/clients.twig', [
            'opt' => $opt
        ]);
    }
}
