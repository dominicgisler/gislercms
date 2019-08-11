<?php

namespace GislerCMS\Controller;

use GislerCMS\Model\Language;
use GislerCMS\Model\PageTranslation;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PageLangControllerAdmin
 * @package GislerCMS\Controller
 */
class PageLangController extends AbstractController
{
    const NAME = 'index';
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
        $page = PageTranslation::getByName($name, Language::getLanguage($lang), true);
        if ($page->getPageTranslationId() == 0 || !$page->getPage()->isEnabled()) {
            $page = PageTranslation::getDefaultByName('error-404', true);
            $response = $response->withStatus(404);
        }
        return $this->render($request, $response, 'layout.twig', ['page' => $page]);
    }
}
