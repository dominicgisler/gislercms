<?php

namespace GislerCMS\Controller;

use GislerCMS\Model\Page;
use GislerCMS\Model\PageTranslation;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class SitemapController
 * @package GislerCMS\Controller
 */
class SitemapController extends AbstractController
{
    const NAME = 'sitemap';
    const PATTERN = '/sitemap.xml';
    const METHODS = ['GET'];

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws \Exception
     */
    public function __invoke($request, $response)
    {
        $arr = [];

        $pages = Page::getWhere('`p`.`trash` = 0 AND `p`.`enabled` = 1');
        foreach ($pages as $page) {
            $trans = PageTranslation::getPageTranslations($page);
            foreach ($trans as $obj) {
                if ($obj->isEnabled()) {
                    $arr[] = $obj;
                }
            }
        }

        return $this->render(
            $request,
            $response->withAddedHeader('Content-Type', 'text/xml'),
            'sitemap.twig',
            ['pages' => $arr]
        );
    }
}
