<?php

namespace GislerCMS\Controller\Admin\Misc\System;

use GislerCMS\Controller\Admin\AbstractController;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ChangelogController
 * @package GislerCMS\Controller\Admin\Misc\System
 */
class ChangelogController extends AbstractController
{
    const NAME = 'admin-misc-system-changelog';
    const PATTERN = '{admin_route}/misc/system/changelog';
    const METHODS = ['GET'];

    const API_RELEASES_URL = 'https://api.github.com/repos/dominicgisler/gislercms/releases';

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $rel = $this->getReleases();

        return $this->render($request, $response, 'admin/misc/system/changelog.twig', [
            'releases' => $rel
        ]);
    }

    /**
     * @return mixed
     */
    private function getReleases(): mixed
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP'
                ]
            ]
        ]);
        $content = file_get_contents(self::API_RELEASES_URL, false, $context);
        return json_decode($content, true);
    }
}
