<?php

namespace GislerCMS\Controller\Admin\Misc;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Config;
use Slim\Http\Request;
use Slim\Http\Response;

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

        if ($request->isPost()) {
            $theme = $request->getParsedBodyParam('select');
            if (is_dir($path . '/' . $theme)) {
                $config = Config::getConfig('theme', 'name');
                $config->setValue($theme);
                $res = $config->save();
                if (!is_null($res)) {
                    $msg = 'save_success';
                    $current = $theme;
                } else {
                    $msg = 'save_error';
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
                'screenshot' => $this->get('base_url') . '/img/admin/theme-noscreen.png'
            ];
            if (file_exists($cfgFile)) {
                $cfg = json_decode(file_get_contents($cfgFile), true);
                if ($cfg) {
                    $themes[$name] = $cfg;
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
