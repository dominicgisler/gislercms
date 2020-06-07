<?php

namespace GislerCMS\Controller\Admin\Misc;

use GislerCMS\Controller\Admin\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SysInfoController
 * @package GislerCMS\Controller\Admin\Misc
 */
class SysInfoController extends AbstractController
{
    const NAME = 'admin-misc-sysinfo';
    const PATTERN = '{admin_route}/misc/sysinfo';
    const METHODS = ['GET', 'POST'];

    const RELEASE_URL = 'https://api.github.com/repos/dominicgisler/gislercms/releases/latest';

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $cmsVersion = $this->get('settings')['version'];

        $update = [];
        $release = $this->getLatestRelease();
        if (!empty($release['tag_name']) && $release['tag_name'] != $cmsVersion) {
            $update = [
                'current' => $cmsVersion,
                'latest' => $release['tag_name']
            ];
        }

        $data = [
            'CMS Version' => $cmsVersion,
            'PHP Version' => phpversion(),
            'MySQL Version' => $this->get('pdo')->query('select version()')->fetchColumn(),
            'Webserver' => $_SERVER['SERVER_SOFTWARE'],
            'URL' => $this->get('base_url'),
            'Verwaltungs-URL' => $this->get('base_url') . $this->get('settings')['global']['admin_route'],
            'Verzeichnis' => $_SERVER['DOCUMENT_ROOT'],
            'PHP Erweiterungen' => '- ' . join('<br>- ', get_loaded_extensions()),
            'HTTP Protokoll' => $_SERVER['SERVER_PROTOCOL'],
            'Servername' => $_SERVER['SERVER_NAME'],
            'Serveradresse' => $_SERVER['SERVER_ADDR'],
            'Serverport' => $_SERVER['SERVER_PORT'],
            'Serversignatur' => $_SERVER['SERVER_SIGNATURE']
        ];

        return $this->render($request, $response, 'admin/misc/sysinfo.twig', [
            'data' => $data,
            'update' => $update
        ]);
    }

    /**
     * @return mixed
     */
    private function getLatestRelease()
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP'
                ]
            ]
        ]);
        $content = file_get_contents(self::RELEASE_URL, false, $context);
        return json_decode($content, true);
    }
}
