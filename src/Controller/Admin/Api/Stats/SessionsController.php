<?php

namespace GislerCMS\Controller\Admin\Api\Stats;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Session;
use GislerCMS\Model\Visit;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SessionsController
 * @package GislerCMS\Controller\Admin\Api\Stats
 */
class SessionsController extends AbstractController
{
    const NAME = 'admin-api-stats-sessions';
    const PATTERN = '{admin_route}/api/stats/sessions[/{id:[0-9]+}]';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $id = (int) $request->getAttribute('route')->getArgument('id');
        $stats = [];

        $draw = $request->getQueryParam('draw');
        $start = intval($request->getQueryParam('start'));
        $length = intval($request->getQueryParam('length'));
        $search = $request->getQueryParam('search')['value'];
        $columns = $request->getQueryParam('columns');
        $order = $request->getQueryParam('order')[0];

        $orderCol = $columns[$order['column']]['data'];
        $orderDir = $order['dir'];

        $search = '%' . $search . '%';
        $recordsTotal = sizeof(Session::getAll());
        if ($id > 0) {
            $recordsFiltered = sizeof(Session::getWhere('`fk_client_id` = ? AND (platform LIKE ? OR browser LIKE ? OR user_agent LIKE ?)', [$search, $search, $search, $id]));
            $sessions = Session::getWhere(sprintf('`fk_client_id` = ? AND (platform LIKE ? OR browser LIKE ? OR user_agent LIKE ?) ORDER BY %s %s LIMIT %d, %d', $orderCol, $orderDir, $start, $length), [$id, $search, $search, $search]);
        } else {
            $recordsFiltered = sizeof(Session::getWhere('(platform LIKE ? OR browser LIKE ? OR user_agent LIKE ?)', [$search, $search, $search]));
            $sessions = Session::getWhere(sprintf('(platform LIKE ? OR browser LIKE ? OR user_agent LIKE ?) ORDER BY %s %s LIMIT %d, %d', $orderCol, $orderDir, $start, $length), [$search, $search, $search]);
        }

        $adminURL = $this->get('base_url') . $this->get('settings')['global']['admin_route'];
        foreach ($sessions as $session) {
            $time = strtotime($session->getUpdatedAt()) - strtotime($session->getCreatedAt());
            $hours = round($time / 3600);
            $mins = round(($time % 3600) / 60);
            $secs = ($time % 3600 % 60);
            $duration = ($hours < 10 ? '0' . $hours : $hours) . ':' . ($mins < 10 ? '0' . $mins : $mins) . ':' . ($secs < 10 ? '0' . $secs : $secs);

            $stats[] = [
                'session_id' => $session->getSessionId(),
                'created_at' => date('d.m.Y H:i:s', strtotime($session->getCreatedAt())),
                'updated_at' => date('d.m.Y H:i:s', strtotime($session->getUpdatedAt())),
                'client_id' => sprintf('<a href="%s/stats/sessions/%d">#%d</a>', $adminURL, $session->getClient()->getClientId(), $session->getClient()->getClientId()),
                'ip' => sprintf('<a href="https://ipinfo.io/%s" target="_blank">%s</a>', $session->getIp(), $session->getIp()),
                'platform' => $session->getPlatform(),
                'browser' => $session->getBrowser(),
                'user_agent' => $session->getUserAgent(),
                'duration' => $duration,
                'visits' => sprintf('<a href="%s/stats/visits/%d">%d</a>', $adminURL, $session->getSessionId(), sizeof(Visit::getWhere('`fk_session_id` = ?', [$session->getSessionId()])))
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
