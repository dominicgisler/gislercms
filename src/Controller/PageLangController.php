<?php

namespace GislerCMS\Controller;

use GislerCMS\Application;
use GislerCMS\Model\Config;
use GislerCMS\Model\Language;
use GislerCMS\Model\PageTranslation;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PageLangController
 * @package GislerCMS\Controller
 */
class PageLangController extends AbstractController
{
    const NAME = 'page-lang';
    const PATTERN = '/{lang:[a-z]{2}}/{page:.*}';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $lang = $request->getAttribute('route')->getArgument('lang');
        $name = $request->getAttribute('route')->getArgument('page');
        Application::setTransLocale($lang);

        $maint = Config::getConfig('global', 'maintenance_mode');
        if ($maint->getValue()) {
            return $this->render($request, $response, 'maintenance.twig');
        }

        $page = PageTranslation::getByName($name, Language::getLanguage($lang));
        return $this->renderPage($request, $response, $page, $name);
    }
}
