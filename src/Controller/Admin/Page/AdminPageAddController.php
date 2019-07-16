<?php

namespace GislerCMS\Controller\Admin\Page;

use GislerCMS\Controller\Admin\AdminAbstractController;
use GislerCMS\Model\Language;
use GislerCMS\Model\Page;
use GislerCMS\Model\PageTranslation;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AdminPageAddControllerAdmin
 * @package GislerCMS\Controller
 */
class AdminPageAddController extends AdminAbstractController
{
    const NAME = 'admin-page-add';
    const PATTERN = '{admin_route}/page/add';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $langs = Language::getAll();

        $page = new Page(0, 'Neue Seite', false, false, $langs[0]);
        $page = $page->save();
        if ($page instanceof Page) {
            foreach ($langs as $lang) {
                $pageTranslation = new PageTranslation(0, $page, $lang, 'new_page', 'Neue Seite');
                $pageTranslation->save();
            }
        }
        return $response->withRedirect($this->get('base_url') . $this->get('settings')['admin_route'] . '/page/' . $page->getPageId());
    }
}
