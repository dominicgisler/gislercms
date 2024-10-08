<?php

namespace GislerCMS\Controller\Admin\Misc\System;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Helper\FileSystemHelper;
use GislerCMS\Helper\MigrationHelper;
use GislerCMS\Helper\SessionHelper;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use ZipArchive;

/**
 * Class UpdateController
 * @package GislerCMS\Controller\Admin\Misc\System
 */
class UpdateController extends AbstractController
{
    const NAME = 'admin-misc-system-update';
    const PATTERN = '{admin_route}/misc/system/update';
    const METHODS = ['GET', 'POST'];

    const API_RELEASE_URL = 'https://api.github.com/repos/dominicgisler/gislercms/releases/latest';
    const API_DEV_RELEASE_URL = 'https://api.github.com/repos/dominicgisler/gislercms/releases/tags/dev-latest';
    const UPDATE_FOLDER = 'update';

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $update = ['type' => 'unavailable'];
        $cnt = SessionHelper::getContainer();
        if ($cnt->offsetExists('update_success')) {
            $cnt->offsetUnset('update_success');
            $update = ['type' => 'updated'];
        } else if ($cnt->offsetExists('update_failed')) {
            $cnt->offsetUnset('update_failed');
            $update = ['type' => 'failed'];
        }

        $rootPath = realpath($this->get('settings')['root_path']);
        $cmsVersion = $this->get('settings')['version'];
        $release = $this->getRelease(self::API_RELEASE_URL);
        $releaseDev = $this->getRelease(self::API_DEV_RELEASE_URL);

        $update['current'] = $cmsVersion;
        $update['latest'] = '';
        $update['release_notes'] = '';
        $update['url'] = '';
        $update['filename'] = '';
        $update['dev_latest'] = '';
        $update['dev_release_notes'] = '';
        $update['dev_url'] = '';
        $update['dev_filename'] = '';

        if (!empty($release['tag_name'])) {
            $update['latest'] = $release['tag_name'];
            $update['release_notes'] = $release['body'];
            foreach ($release['assets'] as $asset) {
                if (empty($update['url']) && $asset['name'] == 'gislercms.zip') {
                    $update['url'] = $asset['browser_download_url'];
                    $update['filename'] = $asset['name'];
                }
            }
        }

        if (!empty($releaseDev['tag_name'])) {
            $update['dev_latest'] = $releaseDev['tag_name'];
            $update['dev_release_notes'] = $releaseDev['body'];
            foreach ($releaseDev['assets'] as $asset) {
                if (empty($update['dev_url']) && $asset['name'] == 'gislercms.zip') {
                    $update['dev_url'] = $asset['browser_download_url'];
                    $update['dev_filename'] = $asset['name'];
                }
            }
        }

        if ($update['type'] == 'unavailable') {
            if ($cmsVersion === 'dev-latest') {
                $update['type'] = 'usingdev';
            } else if ($update['latest'] != '' && $update['current'] != $update['latest']) {
                $update['type'] = 'newupdate';
            } else if ($update['latest'] != '') {
                $update['type'] = 'uptodate';
            }
        }

        if ($request->isPost() && (!is_null($request->getParsedBodyParam('update')) || !is_null($request->getParsedBodyParam('dev-update')))) {
            $updatePath = sprintf('%s/%s', $rootPath, self::UPDATE_FOLDER);
            if (!is_null($request->getParsedBodyParam('dev-update'))) {
                $this->downloadRelease($update['dev_url'], $updatePath, $update['dev_filename']);
            } else {
                $this->downloadRelease($update['url'], $updatePath, $update['filename']);
            }
            $this->installUpdate($rootPath, $updatePath);
            $res = MigrationHelper::executeMigrations($this->get('pdo'));
            if ($res['status'] == 'success') {
                $cnt->offsetSet('update_success', true);
            } else {
                $cnt->offsetSet('update_failed', true);
            }

            return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/misc/system/update');
        }

        return $this->render($request, $response, 'admin/misc/system/update.twig', [
            'update' => $update
        ]);
    }

    /**
     * @param string $url
     * @return mixed
     */
    public static function getRelease(string $url): mixed
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP'
                ]
            ]
        ]);
        $content = file_get_contents($url, false, $context);
        return json_decode($content, true);
    }

    /**
     * @param string $url
     * @param string $dir
     * @param string $filename
     * @return void
     */
    private function downloadRelease(string $url, string $dir, string $filename): void
    {
        $dlPath = sprintf('%s/%s', $dir, $filename);
        foreach (glob($dir . '/{,.}[!.,!..]*', GLOB_BRACE) as $file) {
            FileSystemHelper::remove($file);
        }
        file_put_contents($dlPath, file_get_contents($url));

        $zip = new ZipArchive();
        $res = $zip->open($dlPath);
        if ($res === true) {
            $zip->extractTo($dir);
            $zip->close();
        }
        FileSystemHelper::remove($dlPath);
    }

    /**
     * @param string $rootPath
     * @param string $updatePath
     * @return void
     */
    private function installUpdate(string $rootPath, string $updatePath): void
    {
        FileSystemHelper::remove($rootPath . '/cache');
        FileSystemHelper::remove($rootPath . '/mysql');
        FileSystemHelper::remove($rootPath . '/src');
        FileSystemHelper::remove($rootPath . '/templates');
        FileSystemHelper::remove($rootPath . '/translations');
        FileSystemHelper::remove($rootPath . '/vendor');
        FileSystemHelper::remove($rootPath . '/LICENSE');
        FileSystemHelper::remove($rootPath . '/README.md');
        FileSystemHelper::remove($rootPath . '/public/css');
        FileSystemHelper::remove($rootPath . '/public/editor');
        FileSystemHelper::remove($rootPath . '/public/img');
        FileSystemHelper::remove($rootPath . '/public/js');

        $this->moveFolder($rootPath, $updatePath, $updatePath);
    }

    /**
     * @param string $rootPath
     * @param string $updatePath
     * @param string $subPath
     * @return void
     */
    private function moveFolder(string $rootPath, string $updatePath, string $subPath): void
    {
        foreach (glob($subPath . '/{,.}[!.,!..]*', GLOB_BRACE) as $file) {
            $relPath = substr($file, strlen($updatePath) + 1);
            $toPath = $rootPath . '/' . $relPath;
            if (is_file($file) || !file_exists($toPath)) {
                rename($file, $toPath);
            } else {
                $this->moveFolder($rootPath, $updatePath, $file);
                FileSystemHelper::remove($file);
            }
        }
    }
}
