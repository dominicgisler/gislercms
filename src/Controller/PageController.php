<?php

namespace GislerCMS\Controller;

use Exception;
use GislerCMS\Application;
use GislerCMS\Model\Config;
use GislerCMS\Model\Language;
use GislerCMS\Model\PageTranslation;
use GislerCMS\Model\Redirect;
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
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $name = $request->getAttribute('route')->getArgument('page');
        $locale = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        Application::setTransLocale($locale);

        $maint = Config::getConfig('global', 'maintenance_mode');
        if ($maint->getValue()) {
            return $this->render($request, $response, 'maintenance.twig');
        }

        $redirect = Redirect::getByRoute($name);
        if ($redirect->getRedirectId() > 0) {
            $this->trackRedirect($request, $response, $redirect);
            $url = $this->get('base_url') . '/' . $redirect->getLocation();
            if (substr($redirect->getLocation(), 0, 7) === 'http://' || substr($redirect->getLocation(), 0, 8) === 'https://') {
                $url = $redirect->getLocation();
            }
            return $response->withRedirect($url, 301);
        }

        $page = PageTranslation::getByName($name, Language::getLanguage($locale));
        if ($page->getPageTranslationId() == 0) {
            $page = PageTranslation::getDefaultByName($name);
        }

        return $this->renderPage($request, $response, $page, $name);
    }
}
