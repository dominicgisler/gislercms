<?php

namespace GislerCMS\Controller\Admin\Api\Stats;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Client;
use GislerCMS\Model\Session;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ClientsController
 * @package GislerCMS\Controller\Admin\Api\Stats
 */
class ClientsController extends AbstractController
{
    const NAME = 'admin-api-stats-clients';
    const PATTERN = '{admin_route}/api/stats/clients[/{option}]';
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
        $stats = [];

        $draw = $request->getQueryParam('draw');
        $start = intval($request->getQueryParam('start'));
        $length = intval($request->getQueryParam('length'));
        $search = $request->getQueryParam('search')['value'];
        $columns = $request->getQueryParam('columns');
        $order = $request->getQueryParam('order')[0];

        $orderCol = $columns[$order['column']]['data'];
        $orderDir = $order['dir'];

        $recordsTotal = sizeof(Client::getAll());
        if ($opt == 'real') {
            $recordsFiltered = sizeof(Client::getWhere('`created_at` != `updated_at`'));
            $clients = Client::getWhere(sprintf('`created_at` != `updated_at` ORDER BY %s %s LIMIT %d, %d', $orderCol, $orderDir, $start, $length));
        } else {
            $recordsFiltered = $recordsTotal;
            $clients = Client::getWhere(sprintf('1 ORDER BY %s %s LIMIT %d, %d', $orderCol, $orderDir, $start, $length));
        }

        $adminURL = $this->get('base_url') . $this->get('settings')['global']['admin_route'];
        foreach ($clients as $client) {
            $stats[] = [
                'client_id' => $client->getClientId(),
                'created_at' => date('d.m.Y H:i:s', strtotime($client->getCreatedAt())),
                'updated_at' => date('d.m.Y H:i:s', strtotime($client->getUpdatedAt())),
                'sessions' => sprintf('<a href="%s/stats/sessions/%d">%d</a>', $adminURL, $client->getClientId(), sizeof(Session::getWhere('`fk_client_id` = ?', [$client->getClientId()]))),
                'real' => $client->getCreatedAt() != $client->getUpdatedAt() ? '<i class="fa fa-check text-success"></i>' : '<i class="fa fa-times text-danger"></i>'
            ];
        }

        return $response->withJson([
            'draw' => $draw,
            'data' => $stats,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered
        ]);
    }
}
