<?php

namespace GislerCMS\Model;

use Exception;
use GislerCMS\Controller\Module\AbstractModuleController;
use PDO;
use Slim\Http\Request;
use Slim\Views\Twig;
use Twig\Error\LoaderError;

/**
 * Class DbModel
 * @package GislerCMS\Model
 */
abstract class DbModel
{
    /**
     * @var PDO
     */
    private static $pdo;

    /**
     * @var string
     */
    protected static $table = '';

    /**
     * @param PDO $pdo
     */
    public static function init(PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    /**
     * @return PDO
     * @throws Exception
     */
    protected static function getPDO(): PDO
    {
        if (self::$pdo instanceof PDO) {
            return self::$pdo;
        }
        throw new Exception('Please init $pdo first, use DbModel::init($pdo)');
    }

    /**
     * @param string $where
     * @param array $args
     * @return int
     * @throws Exception
     */
    public static function countWhere(string $where = '', array $args = []): int
    {
        $stmt = self::getPDO()->prepare("SELECT COUNT(*) FROM `" . static::$table . "` " . (!empty($where) ? 'WHERE ' . $where : ''));
        $stmt->execute($args);
        return $stmt->fetchColumn();
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function countAll(): int
    {
        return self::countWhere();
    }

    /**
     * @param string $html
     * @param Language $language
     * @return string|null
     * @throws Exception
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
     * @return string
     * @throws LoaderError
     * @throws Exception
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
}
