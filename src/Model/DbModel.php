<?php

namespace GislerCMS\Model;

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
     * @return string|string[]|null
     */
    protected static function replaceWidgets(string $html, Language $language)
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
}
