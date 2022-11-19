<?php

namespace GislerCMS\Controller\Admin\Page;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Page;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class ListController
 * @package GislerCMS\Controller\Admin\Page
 */
class ListController extends AbstractController
{
    const NAME = 'admin-page-list';
    const PATTERN = '{admin_route}/page/list';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $pages = Page::getAvailable();
        return $this->render($request, $response, 'admin/page/list.twig', [
            'pages' => $pages
        ]);
    }
}
