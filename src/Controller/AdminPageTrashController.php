<?php

namespace GislerCMS\Controller;

use GislerCMS\Model\Page;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminPageTrashController
 * @package GislerCMS\Controller
 */
class AdminPageTrashController extends AbstractController
{
    const NAME = 'admin-page-trash';
    const PATTERN = '{admin_route}/page/trash';
    const METHODS = ['GET', 'POST'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        if ($request->isPost()) {
            $method = 'restore';
            if (!is_null($request->getParsedBodyParam('delete'))) {
                $method = 'delete';
            }
            $pages = $request->getParsedBodyParam('page');
            foreach ($pages as $key => $val) {
                if ($val) {
                    $page = Page::get($key);
                    if ($method == 'delete') {
                        $page->delete();
                    } else {
                        $page->setTrash(false);
                        $page->save();
                    }
                }
            }
        }

        $pages = Page::getTrash();
        return $this->render($request, $response, 'admin/page/trash.twig', [
            'trashPages' => $pages
        ]);
    }
}
