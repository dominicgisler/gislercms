<?php

namespace GislerCMS\Controller\Admin;

use GislerCMS\Model\Client;
use GislerCMS\Model\Page;
use GislerCMS\Model\Session;
use GislerCMS\Model\Visit;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class IndexController
 * @package GislerCMS\Controller\Admin
 */
class IndexController extends AbstractController
{
    const NAME = 'admin-index';
    const PATTERN = '{admin_route}[/]';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $clients = Client::getAll();
        $sessions = Session::getAll();
        $visits = Visit::getAll();
        $pages = Page::getAll();
        $stats = [
            'counts' => [
                'clients' => sizeof($clients),
                'sessions' => sizeof($sessions),
                'visits' => sizeof($visits),
                'pages' => sizeof($pages)
            ],
            'pages' => [],
            'platforms' => [],
            'browsers' => [],
            'sessions' => []
        ];

        foreach ($visits as $visit) {
            $pt = $visit->getPageTranslation();
            if (!isset($stats['pages'][$pt->getPageTranslationId()])) {
                $stats['pages'][$pt->getPageTranslationId()] = [
                    'name' => $pt->getName(),
                    'language' => $pt->getLanguage(),
                    'visits' => 0
                ];
            }
            $stats['pages'][$pt->getPageTranslationId()]['visits']++;
        }

        foreach ($sessions as $session) {
            if (!isset($stats['platforms'][$session->getPlatform()])) {
                $stats['platforms'][$session->getPlatform()] = 0;
            }
            $stats['platforms'][$session->getPlatform()]++;

            if (!isset($stats['browsers'][$session->getBrowser()])) {
                $stats['browsers'][$session->getBrowser()] = 0;
            }
            $stats['browsers'][$session->getBrowser()]++;

            $time = strtotime($session->getUpdatedAt()) - strtotime($session->getCreatedAt());
            $hours = round($time / 3600);
            $mins = round(($time % 3600) / 60);
            $secs = ($time % 3600 % 60);
            $duration = ($hours < 10 ? '0' . $hours : $hours) . ':' . ($mins < 10 ? '0' . $mins : $mins) . ':' . ($secs < 10 ? '0' . $secs : $secs);

            $stats['sessions'][] = [
                'ip' => $session->getIp(),
                'platform' => $session->getPlatform(),
                'browser' => $session->getBrowser(),
                'duration' => $duration
            ];
        }

        return $this->render($request, $response, 'admin/index.twig', [
            'stats' => $stats
        ]);
    }
}
