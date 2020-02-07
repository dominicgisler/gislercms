<?php

namespace GislerCMS\Controller;

use GislerCMS\Controller\Module\AbstractModuleController;
use GislerCMS\Model\Module;
use GislerCMS\Model\Page;
use GislerCMS\Model\PageTranslation;
use GislerCMS\Model\Post;
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
                    $index = $obj->getLanguage()->getLocale() . '_' . $obj->getName();
                    $path = $obj->getLanguage()->getLocale() . '/' . $obj->getName();

                    $arr[$index] = [
                        'path' => $path,
                        'lastmod' => date('Y-m-d', strtotime($obj->getUpdatedAt())) . 'T' . date('H:i:sP', strtotime($obj->getUpdatedAt()))
                    ];

                    $html = $obj->getContent();
                    $pattern = '#<pre class="posts">(.*?)</pre>#';
                    while (preg_match($pattern, $html)) {
                        $html = preg_replace_callback($pattern, function ($match) use (&$arr, $obj, $index, $path) {
                            if (!empty($match[1])) {
                                foreach (Post::getByCategory($match[1]) as $post) {
                                    $pTrans = $post->getPostTranslation($obj->getLanguage());
                                    $arr[$index . '_' . $pTrans->getName()] = [
                                        'path' => $path . '/' . $pTrans->getName(),
                                        'lastmod' => date('Y-m-d', strtotime($pTrans->getUpdatedAt())) . 'T' . date('H:i:sP', strtotime($pTrans->getUpdatedAt()))
                                    ];
                                }
                            }
                            return '';
                        }, $html);
                    }

                    $pattern = '#<pre class="module">(.*?)</pre>#';
                    while (preg_match($pattern, $html)) {
                        $html = preg_replace_callback($pattern, function ($match) use (&$arr, $obj, $index, $path) {
                            $res = '';
                            if (isset($match[1])) {
                                $name = $match[1];
                                $mod = Module::getByName($name);
                                if ($mod->getModuleId() > 0) {
                                    $cfg = json_decode($mod->getConfig(), true);
                                    if (!empty($mod->getController()) && is_array($cfg)) {
                                        $cont = '\\GislerCMS\\Controller\\Module\\' . $mod->getController();
                                        if (class_exists($cont) && is_subclass_of($cont, AbstractModuleController::class)) {
                                            if ($mod->getController() == 'GalleryModuleController') {
                                                foreach ($cfg['galleries'] as $key => $gallery) {
                                                    $arr[$index . '_' . $key] = [
                                                        'path' => $path . '/' . $key,
                                                        'lastmod' => date('Y-m-d', strtotime($obj->getUpdatedAt())) . 'T' . date('H:i:sP', strtotime($obj->getUpdatedAt()))
                                                    ];
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            return $res;
                        }, $html);
                    }
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
