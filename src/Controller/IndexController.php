<?php

namespace GislerCMS\Controller;

use GislerCMS\Model\Config;
use GislerCMS\Model\Page;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class IndexController
 * @package GislerCMS\Controller
 */
class IndexController extends AbstractController
{
    const NAME = 'index';
    const PATTERN = '[/]';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $maint = Config::getConfig('global', 'maintenance_mode');
        if ($maint->getValue()) {
            return $this->render($request, $response, 'maintenance.twig');
        }

        if ($maint->getConfigId() == 0) {
            return $this->render($request, $response, 'config-error.twig', [
                'setup_url' => $this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/setup'
            ]);
        }

        $cfg = Config::getConfig('global', 'default_page');
        $page = Page::get($cfg->getValue());
        $pTrans = $page->getDefaultPageTranslation();
        $pTrans->replaceWidgets();
        $pTrans->replaceModules($this->get('settings')['module'], $request, $this->get('view'));
        return $this->render($request, $response, 'layout.twig', ['page' => $pTrans]);
    }
}
