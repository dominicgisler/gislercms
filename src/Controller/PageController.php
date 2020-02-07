<?php

namespace GislerCMS\Controller;

use GislerCMS\Model\Config;
use GislerCMS\Model\Language;
use GislerCMS\Model\PageTranslation;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PageController
 * @package GislerCMS\Controller
 */
class PageController extends AbstractController
{
    const NAME = 'page';
    const PATTERN = '/{page:.*}';
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

        $name = $request->getAttribute('route')->getArgument('page');

        $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        $page = PageTranslation::getByName($name, Language::getLanguage($locale));
        if ($page->getPageTranslationId() == 0) {
            $page = PageTranslation::getDefaultByName($name);
        }

        return $this->renderPage($request, $response, $page, $name);
    }
}
