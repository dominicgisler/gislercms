<?php

namespace GislerCMS\Controller\Admin\Misc;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Helper\FileSystemHelper;
use GislerCMS\Model\Config;
use Slim\Http\Request;
use Slim\Http\Response;
use ZipArchive;

/**
 * Class ThemeController
 * @package GislerCMS\Controller\Admin\Misc
 */
class ThemeController extends AbstractController
{
    const NAME = 'admin-misc-theme';
    const PATTERN = '{admin_route}/misc/theme';
    const METHODS = ['GET', 'POST'];

    const THEME_PATH = __DIR__ . '/../../../../themes';
    const THEME_CONFIG = 'theme.json';
    const THEME_SCREEN = 'screenshot.png';

    const DEFAULT = 'gcms-default';

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $path = realpath(self::THEME_PATH);
        $msg = false;
        $current = $this->get('settings')['theme']['name'];
        $nodelete = [self::DEFAULT];
        $nodelete[] = $current;

        if ($request->isPost()) {
            if (!is_null($request->getParsedBodyParam('upload'))) {
                $msg = 'upload_error';
                $upload = $request->getUploadedFiles()['new_theme'];
                $overwrite = boolval($request->getParsedBodyParam('overwrite'));
                if ($upload->getClientMediaType() == 'application/zip') {
                    $zipPath = $path . '/' . $upload->getClientFilename();
                    $info = pathinfo($zipPath);
                    $upload->moveTo($zipPath);
                    $name = $info['filename'];

                    $themePath = $path . '/' . $name;
                    if ($name != self::DEFAULT && (!file_exists($themePath) || $overwrite)) {
                        FileSystemHelper::remove($themePath);
                        $zip = new ZipArchive();
                        $res = $zip->open($zipPath);
                        if ($res === true) {
                            $zip->extractTo($themePath);
                            $zip->close();
                            $msg = 'upload_success';
                        }
                    }
                    FileSystemHelper::remove($zipPath);
                }
            } else if (!is_null($request->getParsedBodyParam('select'))) {
                $theme = $request->getParsedBodyParam('select');
                if (is_dir($path . '/' . $theme)) {
                    $config = Config::getConfig('theme', 'name');
                    $config->setValue($theme);
                    $res = $config->save();
                    if (!is_null($res)) {
                        $current = $theme;
                        $nodelete = [self::DEFAULT];
                        $nodelete[] = $current;
                        $cacheDir = $this->get('settings')['data_cache'];
                        $caches = array_filter(glob($cacheDir . '/*'), 'is_dir');
                        foreach ($caches as $cache) {
                            FileSystemHelper::remove($cache);
                        }
                        $msg = 'save_success';
                    } else {
                        $msg = 'save_error';
                    }
                }
            } else if (!is_null($request->getParsedBodyParam('delete'))) {
                $theme = $request->getParsedBodyParam('delete');
                if (!in_array($theme, $nodelete)) {
                    FileSystemHelper::remove($path . '/' . $theme);
                    $msg = 'delete_success';
                } else {
                    $msg = 'delete_error';
                }
            }
        }

        $dirs = array_filter(glob($path . '/*'), 'is_dir');

        $themes = [];
        foreach ($dirs as $dir) {
            $cfgFile = $dir . '/' . self::THEME_CONFIG;
            $screenshot = $dir . '/' . self::THEME_SCREEN;
            $name = basename($dir);
            $themes[$name] = [
                'deletable' => !in_array($name, $nodelete),
                'screenshot' => $this->get('base_url') . '/img/admin/theme-noscreen.png'
            ];
            if (file_exists($cfgFile)) {
                $cfg = json_decode(file_get_contents($cfgFile), true);
                if ($cfg) {
                    $themes[$name] = array_merge($themes[$name], $cfg);
                }
            }
            if (file_exists($screenshot)) {
                $type = pathinfo($screenshot, PATHINFO_EXTENSION);
                $data = file_get_contents($screenshot);
                $themes[$name]['screenshot'] = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        }

        return $this->render($request, $response, 'admin/misc/theme.twig', [
            'themes' => $themes,
            'current' => $current,
            'message' => $msg
        ]);
    }
}
