<?php

namespace GislerCMS\Controller;

use GislerCMS\Model\Config;
use GislerCMS\Model\Page;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class IndexControllerAdmin
 * @package GislerCMS\Controller
 */
class IndexController extends AbstractController
{
    const NAME = 'index';
    const PATTERN = '[/]';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $cfg = Config::getConfig('default_page');
        $page = Page::get($cfg->getValue());
        return $this->render($request, $response, 'layout.twig', ['page' => $page->getDefaultPageTranslation()]);
    }
}
