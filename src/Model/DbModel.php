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
     * @param array $modules
     * @param Request $request
     * @param Twig $view
     * @return string|null
     */
    protected static function replaceModulePlaceholders(string $html, array $modules, Request $request, Twig $view): string
    {
        $pattern = '#<pre class="module">(.*?)</pre>#';
        while (preg_match($pattern, $html)) {
            $html = preg_replace_callback($pattern, function ($match) use ($modules, $request, $view) {
                $res = '';
                if (isset($match[1])) {
                    $name = $match[1];
                    if (array_key_exists($name, $modules)) {
                        $mod = $modules[$name];
                        if (!empty($mod['controller']) && is_array($mod['config'])) {
                            $cont = '\\GislerCMS\\Controller\\Module\\' . $mod['controller'];
                            if (class_exists($cont) && is_subclass_of($cont, AbstractModuleController::class)) {
                                /** @var AbstractModuleController $class */
                                $class = new $cont($mod['config'], $view);
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
}
