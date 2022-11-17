<?php

namespace GislerCMS\Controller\Admin\Misc\System;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Helper\FileSystemHelper;
use GislerCMS\Helper\MigrationHelper;
use GislerCMS\Helper\SessionHelper;
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
    const UPDATE_FOLDER = 'update';

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
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
        $release = $this->getRelease();
        if (!empty($release['tag_name'])) {
            $update['current'] = $cmsVersion;
            $update['latest'] = $release['tag_name'];
            $update['url'] = '';
            $update['filename'] = '';
            $update['release_notes'] = $release['body'];
            foreach ($release['assets'] as $asset) {
                if (empty($update['url']) && $asset['name'] == 'gislercms.zip') {
                    $update['url'] = $asset['browser_download_url'];
                    $update['filename'] = $asset['name'];
                }
            }
            if ($update['type'] == 'unavailable') {
                $update['type'] = 'uptodate';
                if ($update['current'] != $update['latest']) {
                    $update['type'] = 'newupdate';
                }
            }

            if ($request->isPost() && !is_null($request->getParsedBodyParam('update'))) {
                $updatePath = sprintf('%s/%s', $rootPath, self::UPDATE_FOLDER);
                $this->downloadRelease($update['url'], $updatePath, $update['filename']);
                $this->installUpdate($rootPath, $updatePath);
                $res = MigrationHelper::executeMigrations($this->get('pdo'));
                if ($res['status'] == 'success') {
                    $cnt->offsetSet('update_success', true);
                } else {
                    $cnt->offsetSet('update_failed', true);
                }

                return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/misc/system/update');
            }
        }

        return $this->render($request, $response, 'admin/misc/system/update.twig', [
            'update' => $update
        ]);
    }

    /**
     * @return mixed
     */
    public static function getRelease()
    {
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'header' => [
                    'User-Agent: PHP'
                ]
            ]
        ]);
        $content = file_get_contents(self::API_RELEASE_URL, false, $context);
        return json_decode($content, true);
    }

    /**
     * @param string $url
     * @param string $dir
     * @param string $filename
     */
    private function downloadRelease(string $url, string $dir, string $filename)
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
     */
    private function installUpdate(string $rootPath, string $updatePath)
    {
        FileSystemHelper::remove($rootPath . '/cache');
        FileSystemHelper::remove($rootPath . '/mysql');
        FileSystemHelper::remove($rootPath . '/src');
        FileSystemHelper::remove($rootPath . '/templates');
        FileSystemHelper::remove($rootPath . '/translations');
        FileSystemHelper::remove($rootPath . '/vendor');
        FileSystemHelper::remove($rootPath . '/LICENSE');
        FileSystemHelper::remove($rootPath . '/README.md');

        $this->moveFolder($rootPath, $updatePath, $updatePath);
    }

    /**
     * @param string $rootPath
     * @param string $updatePath
     * @param string $subPath
     */
    private function moveFolder(string $rootPath, string $updatePath, string $subPath)
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
