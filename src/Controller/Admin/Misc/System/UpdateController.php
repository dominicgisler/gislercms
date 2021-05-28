<?php

namespace GislerCMS\Controller\Admin\Misc\System;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Helper\SessionHelper;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
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
        }

        $rootPath = $this->get('settings')['root_path'];
        $cmsVersion = $this->get('settings')['version'];
        $release = $this->getRelease();
        if (!empty($release['tag_name'])) {
            $update['current'] = $cmsVersion;
            $update['latest'] = $release['tag_name'];
            $update['url'] = '';
            $update['filename'] = '';
            $update['release_notes'] = $release['body'];
            foreach ($release['assets'] as $asset) {
                if (empty($update['url']) &&
                    $asset['content_type'] == 'application/zip' &&
                    substr($asset['name'], 0, strlen('gislercms')) === 'gislercms'
                ) {
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

                $cnt->offsetSet('update_success', true);
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
    public function downloadRelease(string $url, string $dir, string $filename)
    {
        $dlPath = sprintf('%s/%s', $dir, $filename);
        $this->remove($dir);
        mkdir($dir);
        file_put_contents($dlPath, file_get_contents($url));

        $zip = new ZipArchive();
        $res = $zip->open($dlPath);
        if ($res === true) {
            $zip->extractTo($dir);
            $zip->close();
        }
    }

    /**
     * @param string $path
     */
    private function remove(string $path)
    {
        if (file_exists($path)) {
            if (is_dir($path)) {
                $it = new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS);
                $files = new RecursiveIteratorIterator($it,
                    RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($files as $file) {
                    if ($file->isDir()) {
                        rmdir($file->getRealPath());
                    } else {
                        unlink($file->getRealPath());
                    }
                }
                rmdir($path);
            } else if (is_file($path)) {
                unlink($path);
            }
        }
    }
}
