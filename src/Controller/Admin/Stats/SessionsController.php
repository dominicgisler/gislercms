<?php

namespace GislerCMS\Controller\Admin\Stats;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Session;
use GislerCMS\Model\Visit;
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
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $id = (int) $request->getAttribute('route')->getArgument('id');
        if ($id > 0) {
            $sessions = Session::getWhere('`fk_client_id` = ?', [$id]);
        } else {
            $sessions = Session::getAll();
        }
        $stats = [];

        foreach ($sessions as $session) {
            $time = strtotime($session->getUpdatedAt()) - strtotime($session->getCreatedAt());
            $hours = round($time / 3600);
            $mins = round(($time % 3600) / 60);
            $secs = ($time % 3600 % 60);
            $duration = ($hours < 10 ? '0' . $hours : $hours) . ':' . ($mins < 10 ? '0' . $mins : $mins) . ':' . ($secs < 10 ? '0' . $secs : $secs);

            $stats[] = [
                'session_id' => $session->getSessionId(),
                'created_at' => $session->getCreatedAt(),
                'updated_at' => $session->getUpdatedAt(),
                'client_id' => $session->getClient()->getClientId(),
                'ip' => $session->getIp(),
                'platform' => $session->getPlatform(),
                'browser' => $session->getBrowser(),
                'duration' => $duration,
                'visits' => sizeof(Visit::getWhere('`fk_session_id` = ?', [$session->getSessionId()]))
            ];
        }

        return $this->render($request, $response, 'admin/stats/sessions.twig', [
            'stats' => $stats
        ]);
    }
}
