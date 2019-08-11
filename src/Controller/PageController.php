<?php

namespace GislerCMS\Controller;

use GislerCMS\Model\PageTranslation;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class PageControllerAdmin
 * @package GislerCMS\Controller
 */
class PageController extends AbstractController
{
    const NAME = 'index';
    const PATTERN = '/{page}';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $name = $request->getAttribute('route')->getArgument('page');
        $page = PageTranslation::getDefaultByName($name, true);
        return $this->render($request, $response, 'layout.twig', ['page' => $page]);
    }
}
