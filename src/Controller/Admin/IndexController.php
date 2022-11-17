<?php

namespace GislerCMS\Controller\Admin;

use Exception;
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
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $track = Config::getConfig('global', 'enable_tracking')->getValue();

        $cacheFile = $this->get('settings')['data_cache'] . 'dashboard.json';
        if (file_exists($cacheFile)) {
            $cache = json_decode(file_get_contents($cacheFile), true);
        } else {
            $cache = [
                'calculation_date' => '',
                'stats' => [
                    'counts' => [
                        'clients' => 0,
                        'real_clients' => 0,
                        'sessions' => 0,
                        'visits' => 0
                    ],
                    'pages' => [],
                    'platforms' => [],
                    'browsers' => []
                ],
                'graph' => self::mapGraphData([
                    'sessions' => [],
                    'visits' => [],
                    'clients' => []
                ])
            ];
        }

        if ($request->isPost() && !is_null($request->getParsedBodyParam('calculate'))) {
            self::calculateStats($cacheFile);

            return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route']);
        }

        return $this->render($request, $response, 'admin/index.twig', [
            'tracking' => $track,
            'calculation_date' => $cache['calculation_date'],
            'stats' => $cache['stats'],
            'graph' => $cache['graph']
        ]);
    }

    /**
     * @param string $cacheFile
     * @throws Exception
     */
    public static function calculateStats(string $cacheFile)
    {
        $stats = [
            'counts' => [
                'clients' => Client::countAll(),
                'real_clients' => Client::countReal(),
                'sessions' => Session::countAll(),
                'visits' => Visit::countAll()
            ],
            'pages' => Visit::getPageVisits(),
            'platforms' => Session::countPlatforms(),
            'browsers' => Session::countBrowsers()
        ];
        $graph = [
            'sessions' => [
                'year' => Session::countByTimestamp('%Y'),
                'month' => Session::countByTimestamp('%Y-%m'),
                'day' => Session::countByTimestamp('%Y-%m-%d'),
                'hour' => Session::countByTimestamp('%Y-%m-%d %H:00')
            ],
            'visits' => [
                'year' => Visit::countByTimestamp('%Y'),
                'month' => Visit::countByTimestamp('%Y-%m'),
                'day' => Visit::countByTimestamp('%Y-%m-%d'),
                'hour' => Visit::countByTimestamp('%Y-%m-%d %H:00')
            ],
            'clients' => [
                'year' => Client::countByTimestamp('%Y'),
                'month' => Client::countByTimestamp('%Y-%m'),
                'day' => Client::countByTimestamp('%Y-%m-%d'),
                'hour' => Client::countByTimestamp('%Y-%m-%d %H:00')
            ]
        ];

        file_put_contents($cacheFile, json_encode([
            'calculation_date' => date('Y-m-d H:i:s'),
            'stats' => mb_convert_encoding($stats, "UTF-8", "UTF-8"),
            'graph' => self::mapGraphData($graph)
        ]));
    }

    /**
     * @param array $graph
     * @return array
     */
    private static function mapGraphData(array $graph): array
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
                    if ($pos !== false) {
                        $graphData[$int]['data'][$type][$pos] = $count;
                    }
                }
            }
        }
        return $graphData;
    }
}
