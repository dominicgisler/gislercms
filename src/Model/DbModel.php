<?php

namespace GislerCMS\Model;

use GislerCMS\Controller\Module\AbstractModuleController;
use Slim\Http\Request;
use Slim\Views\Twig;

/**
 * Class DbModel
 * @package GislerCMS\Model
 */
abstract class DbModel
{
    /**
     * @var \PDO
     */
    private static $pdo;

    /**
     * @param \PDO $pdo
     */
    public static function init(\PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    /**
     * @return \PDO
     * @throws \Exception
     */
    protected static function getPDO(): \PDO
    {
        if (self::$pdo instanceof \PDO) {
            return self::$pdo;
        }
        throw new \Exception('Please init $pdo first, use DbModel::init($pdo)');
    }

    /**
     * @param string $html
     * @param Language $language
     * @return string|null
     */
    protected static function replaceWidgetPlaceholders(string $html, Language $language): string
    {
        $pattern = '#<pre class="widget">(.*?)</pre>#';
        while (preg_match($pattern, $html)) {
            $html = preg_replace_callback($pattern, function ($match) use ($language) {
                $res = '';
                if (isset($match[1])) {
                    $widget = Widget::getWidget($match[1]);
                    if ($widget->getWidgetId() > 0) {
                        $trans = WidgetTranslation::getWidgetTranslation($widget, $language);
                        $res = $trans->getContent();
                    }
                }
                return $res;
            }, $html);
        }
        return $html;
    }

    /**
     * @param string $html
     * @param Request $request
     * @param Twig $view
     * @return string|null
     */
    protected static function replaceModulePlaceholders(string $html, Request $request, Twig $view): string
    {
        $pattern = '#<pre class="module">(.*?)</pre>#';
        while (preg_match($pattern, $html)) {
            $html = preg_replace_callback($pattern, function ($match) use ($request, $view) {
                $res = '';
                if (isset($match[1])) {
                    $name = $match[1];
                    $mod = Module::getByName($name);
                    if ($mod->getModuleId() > 0) {
                        $cfg = json_decode($mod->getConfig(), true);
                        if (!empty($mod->getController()) && is_array($cfg)) {
                            $cont = '\\GislerCMS\\Controller\\Module\\' . $mod->getController();
                            if (class_exists($cont) && is_subclass_of($cont, AbstractModuleController::class)) {
                                /** @var AbstractModuleController $class */
                                $class = new $cont($cfg, $view);
                                $res = $class->execute($request);
                            }
                        }
                    }
                }
                return $res;
            }, $html);
        }
        return $html;
    }

    /**
     * @param string $html
     * @param PageTranslation $pTrans
     * @param Language $language
     * @param Request $request
     * @param Twig $view
     * @return string|null
     */
    protected static function replacePostsPlaceholders(string $html, PageTranslation $pTrans, Language $language, Request $request, Twig $view): string
    {
        $pattern = '#<pre class="posts">(.*?)</pre>#';
        while (preg_match($pattern, $html)) {
            $html = preg_replace_callback($pattern, function ($match) use ($pTrans, $language, $request, $view) {
                $res = '';
                if (isset($match[1])) {
                    $name = $match[1];
                    $args = $request->getAttribute('arguments');
                    if (strlen($args) > 0) {
                        $trans = PostTranslation::getByName($args, $language);
                        $post = $trans->getPost();
                        if ($post->getPostId() > 0 && (new \DateTime($post->getPublishAt())) < (new \DateTime())) {
                            return $view->fetch('posts/detail.twig', [
                                'post' => $post,
                                'trans' => $trans
                            ]);
                        }
                    }
                    $posts = Post::getByCategory($name);
                    $transList = [];
                    foreach ($posts as $post) {
                        $trans = PostTranslation::getPostTranslation($post, $language);
                        $transList[] = [
                            'post' => $post,
                            'trans' => $trans
                        ];
                    }
                    $res = $view->fetch('posts/list.twig', [
                        'pTrans' => $pTrans,
                        'list' => $transList
                    ]);
                }
                return $res;
            }, $html);
        }
        return $html;
    }
}
