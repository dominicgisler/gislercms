<?php

namespace GislerCMS\Controller\Admin\Page;

use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Model\Config;
use GislerCMS\Model\Language;
use GislerCMS\Model\Page;
use GislerCMS\Model\PageTranslation;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class AddController
 * @package GislerCMS\Controller\Admin\Page
 */
class AddController extends AbstractController
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
                $pageTranslation = new PageTranslation(
                    0,
                    $page,
                    $lang,
                    'new_page_' . microtime(true),
                    'Neue Seite',
                    '',
                    Config::getConfig('page', 'meta_keywords')->getValue(),
                    Config::getConfig('page', 'meta_description')->getValue(),
                    Config::getConfig('page', 'meta_author')->getValue(),
                    Config::getConfig('page', 'meta_copyright')->getValue()
                );
                $res = $pageTranslation->save();
            }
        }
        return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/page/' . $page->getPageId());
    }
}