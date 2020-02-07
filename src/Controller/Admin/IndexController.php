<?php

namespace GislerCMS\Controller\Admin;

use GislerCMS\Model\Client;
use GislerCMS\Model\Config;
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
        $track = Config::getConfig('global', 'enable_tracking')->getValue();

        $clients = Client::getAll();
        $sessions = Session::getAll();
        $visits = Visit::getAll();
        $stats = [
            'counts' => [
                'clients' => sizeof($clients),
                'real_clients' => 0,
                'sessions' => sizeof($sessions),
                'visits' => sizeof($visits)
            ],
            'pages' => [],
            'platforms' => [],
            'browsers' => []
        ];
        $graph = [
            'sessions' => [],
            'visits' => [],
            'clients' => []
        ];

        foreach ($clients as $client) {
            if ($client->getCreatedAt() != $client->getUpdatedAt()) {
                $stats['counts']['real_clients']++;
            }
            $graph['clients'] = $this->countDate($client->getCreatedAt(), $graph['clients']);
        }

        foreach ($visits as $visit) {
            $pt = $visit->getPageTranslation();
            $index = $pt->getPageTranslationId() . $visit->getArguments();
            if (!isset($stats['pages'][$index])) {
                $stats['pages'][$index] = [
                    'page' => $pt->getPage(),
                    'language' => $pt->getLanguage(),
                    'arguments' => $visit->getArguments(),
                    'visits' => 0
                ];
            }
            $stats['pages'][$index]['visits']++;

            $graph['visits'] = $this->countDate($visit->getCreatedAt(), $graph['visits']);
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

            $graph['sessions'] = $this->countDate($session->getCreatedAt(), $graph['sessions']);
        }

        return $this->render($request, $response, 'admin/index.twig', [
            'tracking' => $track,
            'stats' => $stats,
            'graph' => $this->mapGraphData($graph)
        ]);
    }

    /**
     * @param string $date
     * @param array $arr
     * @return array
     */
    private function countDate(string $date, array $arr)
    {
        $timestamp = strtotime($date);
        $times = [
            'year' => date('Y', $timestamp),
            'month' => date('Y-m', $timestamp),
            'day' => date('Y-m-d', $timestamp),
            'hour' => date('Y-m-d H:00', $timestamp),
            'min' => date('Y-m-d H:i', $timestamp)
        ];

        foreach ($times as $key => $time) {
            if (!isset($arr[$key][$time])) {
                $arr[$key][$time] = 0;
            }
            $arr[$key][$time]++;
        }

        return $arr;
    }

    /**
     * @param array $graph
     * @return array
     */
    private function mapGraphData(array $graph)
    {
        $graphData = [];
        foreach (['year', 'month', 'day', 'hour'] as $type) {
            $graphData[$type] = [
                'labels' => [],
                'data' => [
                    'clients' => [],
                    'visits' => [],
                    'sessions' => []
                ]
            ];
        }

        $types = [
            'year' => [
                'format' => 'Y',
                'interval' => 10
            ],
            'month' => [
                'format' => 'Y-m',
                'interval' => 12
            ],
            'day' => [
                'format' => 'Y-m-d',
                'interval' => 30
            ],
            'hour' => [
                'format' => 'Y-m-d H:00',
                'interval' => 24
            ]
        ];
        foreach ($types as $key => $cfg) {
            for ($i = $cfg['interval']; $i >= 0; $i--) {
                $graphData[$key]['labels'][] = date($cfg['format'], strtotime('-' . $i . $key . 's'));
                $graphData[$key]['data']['clients'][] = 0;
                $graphData[$key]['data']['sessions'][] = 0;
                $graphData[$key]['data']['visits'][] = 0;
            }
        }

        foreach ($graph as $type => $times) {
            foreach ($times as $int => $counts) {
                foreach ($counts as $key => $count) {
                    $pos = array_search($key, $graphData[$int]['labels']);
                    if ($pos) {
                        $graphData[$int]['data'][$type][$pos] = $count;
                    }
                }
            }
        }
        return $graphData;
    }
}
