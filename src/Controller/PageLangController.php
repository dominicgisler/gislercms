<?php

namespace GislerCMS\Controller;

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
        $maint = Config::getConfig('global', 'maintenance_mode');
        if ($maint->getValue()) {
            return $this->render($request, $response, 'maintenance.twig');
        }

        $lang = $request->getAttribute('route')->getArgument('lang');
        $name = $request->getAttribute('route')->getArgument('page');
        $page = PageTranslation::getByName($name, Language::getLanguage($lang));
        if ($page->getPageTranslationId() == 0 || !$page->getPage()->isEnabled()) {
            $page = PageTranslation::getDefaultByName('error-404');
            $response = $response->withStatus(404);
        }
        $page->replaceWidgets();
        $page->replaceModules($this->get('settings')['module'], $request, $this->get('view'));
        return $this->render($request, $response, 'layout.twig', ['page' => $page]);
    }
}
