<?php

namespace GislerCMS\Controller\Admin\Stats;

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
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $opt = $request->getAttribute('route')->getArgument('option');
        if ($opt == 'real') {
            $clients = Client::getWhere('`created_at` != `updated_at`');
        } else {
            $clients = Client::getAll();
        }
        $stats = [];

        foreach ($clients as $client) {
            $stats[] = [
                'client_id' => $client->getClientId(),
                'created_at' => $client->getCreatedAt(),
                'updated_at' => $client->getUpdatedAt(),
                'sessions' => sizeof(Session::getWhere('`fk_client_id` = ?', [$client->getClientId()])),
                'real' => $client->getCreatedAt() != $client->getUpdatedAt()
            ];
        }

        return $this->render($request, $response, 'admin/stats/clients.twig', [
            'stats' => $stats
        ]);
    }
}
