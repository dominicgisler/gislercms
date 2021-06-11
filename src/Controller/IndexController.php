<?php

namespace GislerCMS\Controller;

use Exception;
use GislerCMS\Application;
use GislerCMS\Model\Config;
use GislerCMS\Model\Language;
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
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        Application::setTransLocale($locale);

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

        $pTrans = $page->getPageTranslation(Language::getLanguage($locale));
        if ($pTrans->getPageTranslationId() == 0) {
            $pTrans = $page->getDefaultPageTranslation();
        }

        return $this->renderPage($request, $response, $pTrans);
    }
}
