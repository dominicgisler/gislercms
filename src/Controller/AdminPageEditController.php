<?php

namespace GislerCMS\Controller;

use GislerCMS\Model\Page;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminPageEditController
 * @package GislerCMS\Controller
 */
class AdminPageEditController extends AbstractController
{
    const NAME = 'admin-page-edit';
    const PATTERN = '{admin_route}/page/{id:[0-9]+}';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $id = (int) $request->getAttribute('route')->getArgument('id');
        $page = Page::get($id);

        if ($page->getPageId() > 0) {
            return $this->render($request, $response, 'admin/page/edit.twig', [
                'page' => $page
            ]);
        }

        return $this->render($request, $response->withStatus(404), 'admin/page/not-found.twig');
    }
}
