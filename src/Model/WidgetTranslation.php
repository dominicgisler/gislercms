<?php

namespace GislerCMS\Model;

/**
 * Class WidgetTranslation
 * @package GislerCMS\Model
 */
class WidgetTranslation extends DbModel
{
    /**
     * @var int
     */
    private $widgetTranslationId;

    /**
     * @var Widget
     */
    private $widget;

    /**
     * @var Language
     */
    private $language;

    /**
     * @var string
     */
    private $content;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * WidgetTranslation constructor.
     * @param int $widgetTranslationId
     * @param Widget $widget
     * @param Language $language
     * @param string $content
     * @param bool $enabled
     */
    public function __construct(
        int $widgetTranslationId = 0,
        Widget $widget = null,
        Language $language = null,
        string $content = '',
        bool $enabled = false
    ) {
        $this->widgetTranslationId = $widgetTranslationId;
        $this->widget = $widget;
        $this->language = $language;
        $this->content = $content;
        $this->enabled = $enabled;
    }

    /**
     * @param int $id
     * @return WidgetTranslation
     * @throws \Exception
     */
    public static function get(int $id): WidgetTranslation
    {
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`widget_translation_id`,
                `t`.`fk_widget_id`,
                `t`.`content`,
                `t`.`enabled`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled'
            
            FROM `cms__widget_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            WHERE `t`.`widget_translation_id` = ?
        ");
        $stmt->execute([$id]);
        $widgetTranslation = $stmt->fetchObject();
        if ($widgetTranslation) {
            return new WidgetTranslation(
                $widgetTranslation->widget_translation_id,
                Widget::get($widgetTranslation->fk_widget_id),
                new Language(
                    $widgetTranslation->language_id,
                    $widgetTranslation->locale,
                    $widgetTranslation->description,
                    $widgetTranslation->l_enabled
                ),
                $widgetTranslation->content ?: '',
                $widgetTranslation->enabled
            );
        }
        return new WidgetTranslation();
    }

    /**
     * @param Widget $widget
     * @return WidgetTranslation[]
     * @throws \Exception
     */
    public static function getWidgetTranslations(Widget $widget): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`widget_translation_id`,
                `t`.`content`,
                `t`.`enabled`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled'
            
            FROM `cms__widget_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            WHERE `t`.`fk_widget_id` = ?
        ");
        $stmt->execute([$widget->getWidgetId()]);
        $widgetTranslations =  $stmt->fetchAll(\PDO::FETCH_OBJ);
        if (sizeof($widgetTranslations) > 0) {
            foreach ($widgetTranslations as $widgetTranslation) {
                $arr[$widgetTranslation->locale] = new WidgetTranslation(
                    $widgetTranslation->widget_translation_id,
                    $widget,
                    new Language(
                        $widgetTranslation->language_id,
                        $widgetTranslation->locale,
                        $widgetTranslation->description,
                        $widgetTranslation->l_enabled
                    ),
                    $widgetTranslation->content ?: '',
                    $widgetTranslation->enabled
                );
            }
        }
        return $arr;
    }

    /**
     * @param Widget $widget
     * @return WidgetTranslation
     * @throws \Exception
     */
    public static function getDefaultWidgetTranslation(Widget $widget): WidgetTranslation
    {
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`widget_translation_id`,
                `t`.`content`,
                `t`.`enabled`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled'
            
            FROM `cms__widget_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            WHERE `t`.`fk_widget_id` = ?
            AND `l`.`language_id` = ?
        ");
        $stmt->execute([$widget->getWidgetId(), $widget->getLanguage()->getLanguageId()]);
        $widgetTranslation =  $stmt->fetchObject();
        if ($widgetTranslation) {
            return new WidgetTranslation(
                $widgetTranslation->widget_translation_id,
                $widget,
                new Language(
                    $widgetTranslation->language_id,
                    $widgetTranslation->locale,
                    $widgetTranslation->description,
                    $widgetTranslation->l_enabled
                ),
                $widgetTranslation->content ?: '',
                $widgetTranslation->enabled
            );
        }
        return new WidgetTranslation();
    }

    /**
     * @param Widget $widget
     * @param Language $language
     * @return WidgetTranslation
     * @throws \Exception
     */
    public static function getWidgetTranslation(Widget $widget, Language $language): WidgetTranslation
    {
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`widget_translation_id`,
                `t`.`content`,
                `t`.`enabled`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled'
            
            FROM `cms__widget_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            WHERE `t`.`enabled` = 1
            AND `t`.`fk_widget_id` = ?
            AND `l`.`language_id` = ?
        ");
        $stmt->execute([$widget->getWidgetId(), $language->getLanguageId()]);
        $widgetTranslation =  $stmt->fetchObject();
        if ($widgetTranslation) {
            return new WidgetTranslation(
                $widgetTranslation->widget_translation_id,
                $widget,
                new Language(
                    $widgetTranslation->language_id,
                    $widgetTranslation->locale,
                    $widgetTranslation->description,
                    $widgetTranslation->l_enabled
                ),
                $widgetTranslation->content ?: '',
                $widgetTranslation->enabled
            );
        }
        return self::getDefaultWidgetTranslation($widget);
    }

    /**
     * @return WidgetTranslation|null
     * @throws \Exception
     */
    public function save(): ?WidgetTranslation
    {
        $pdo = self::getPDO();
        if ($this->getWidgetTranslationId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__widget_translation`
                SET `content` = ?, `enabled` = ?
                WHERE `widget_translation_id` = ?
            ");
            $res = $stmt->execute([
                $this->getContent(),
                $this->isEnabled(),
                $this->getWidgetTranslationId()
            ]);
            return $res ? self::get($this->getWidgetTranslationId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__widget_translation` (
                    `fk_widget_id`, `fk_language_id`, `content`, `enabled`
                )
                VALUES (
                    ?, ?, ?, ?
                )
            ");
            $res = $stmt->execute([
                $this->getWidget()->getWidgetId(),
                $this->getLanguage()->getLanguageId(),
                $this->getContent(),
                $this->isEnabled()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @return int
     */
    public function getWidgetTranslationId(): int
    {
        return $this->widgetTranslationId;
    }

    /**
     * @param int $widgetTranslationId
     */
    public function setWidgetTranslationId(int $widgetTranslationId): void
    {
        $this->widgetTranslationId = $widgetTranslationId;
    }

    /**
     * @return Widget
     */
    public function getWidget(): Widget
    {
        return $this->widget;
    }

    /**
     * @param Widget $widget
     */
    public function setWidget(Widget $widget): void
    {
        $this->widget = $widget;
    }

    /**
     * @return Language
     */
    public function getLanguage(): Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @param string $content
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled(bool $enabled): void
    {
        $this->enabled = $enabled;
    }
}
