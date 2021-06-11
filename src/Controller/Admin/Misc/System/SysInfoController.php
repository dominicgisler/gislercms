<?php

namespace GislerCMS\Controller\Admin\Misc\System;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SysInfoController
 * @package GislerCMS\Controller\Admin\Misc\System
 */
class SysInfoController extends AbstractController
{
    const NAME = 'admin-misc-system-sysinfo';
    const PATTERN = '{admin_route}/misc/system/sysinfo';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $cmsVersion = $this->get('settings')['version'];

        $iniCheck = ['max_execution_time', 'max_input_time', 'memory_limit', 'post_max_size', 'upload_max_filesize'];
        $phpConfigs = '';
        foreach ($iniCheck as $key) {
            $phpConfigs .= sprintf('%s: %s<br>', $key, ini_get($key));
        }

        $data = [
            'CMS Version' => $cmsVersion,
            'PHP Version' => phpversion(),
            'MySQL Version' => $this->get('pdo')->query('select version()')->fetchColumn(),
            'Webserver' => $_SERVER['SERVER_SOFTWARE'],
            'URL' => $this->get('base_url'),
            'Verwaltungs-URL' => $this->get('base_url') . $this->get('settings')['global']['admin_route'],
            'Verzeichnis' => $_SERVER['DOCUMENT_ROOT'],
            'PHP Konfigurationen' => $phpConfigs,
            'PHP Erweiterungen' => '- ' . join('<br>- ', get_loaded_extensions()),
            'HTTP Protokoll' => $_SERVER['SERVER_PROTOCOL'],
            'Servername' => $_SERVER['SERVER_NAME'],
            'Serveradresse' => $_SERVER['SERVER_ADDR'],
            'Serverport' => $_SERVER['SERVER_PORT'],
            'Serversignatur' => $_SERVER['SERVER_SIGNATURE']
        ];

        return $this->render($request, $response, 'admin/misc/system/sysinfo.twig', [
            'data' => $data
        ]);
    }
}
