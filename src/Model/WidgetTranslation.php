<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use PDOStatement;

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
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * @var string
     */
    protected static $table = 'cms__widget_translation';

    /**
     * WidgetTranslation constructor.
     * @param int $widgetTranslationId
     * @param Widget|null $widget
     * @param Language|null $language
     * @param string $content
     * @param bool $enabled
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int      $widgetTranslationId = 0,
        Widget   $widget = null,
        Language $language = null,
        string   $content = '',
        bool     $enabled = false,
        string   $createdAt = '',
        string   $updatedAt = ''
    )
    {
        $this->widgetTranslationId = $widgetTranslationId;
        $this->widget = $widget;
        $this->language = $language;
        $this->content = $content;
        $this->enabled = $enabled;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return WidgetTranslation[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`widget_translation_id`,
                `t`.`fk_widget_id`,
                `t`.`content`,
                `t`.`enabled`,
                `t`.`created_at`,
                `t`.`updated_at`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled',
                `l`.`created_at` AS 'l_created_at',
                `l`.`updated_at` AS 'l_updated_at'
            
            FROM `cms__widget_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
        ");
        if ($stmt instanceof PDOStatement) {
            $stmt->execute($args);
            $widgetTranslations = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (sizeof($widgetTranslations) > 0) {
                foreach ($widgetTranslations as $widgetTranslation) {
                    $arr[$widgetTranslation->locale] = new WidgetTranslation(
                        $widgetTranslation->widget_translation_id,
                        Widget::get($widgetTranslation->fk_widget_id),
                        new Language(
                            $widgetTranslation->language_id,
                            $widgetTranslation->locale,
                            $widgetTranslation->description,
                            $widgetTranslation->l_enabled,
                            $widgetTranslation->l_created_at,
                            $widgetTranslation->l_updated_at
                        ),
                        $widgetTranslation->content ?: '',
                        $widgetTranslation->enabled,
                        $widgetTranslation->created_at,
                        $widgetTranslation->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return WidgetTranslation
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): WidgetTranslation
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new WidgetTranslation();
    }

    /**
     * @param int $id
     * @return WidgetTranslation
     * @throws Exception
     */
    public static function get(int $id): WidgetTranslation
    {
        return self::getObjectWhere('`t`.`widget_translation_id` = ?', [$id]);
    }

    /**
     * @param Widget $widget
     * @return WidgetTranslation[]
     * @throws Exception
     */
    public static function getWidgetTranslations(Widget $widget): array
    {
        return self::getWhere('`t`.`fk_widget_id` = ?', [$widget->getWidgetId()]);
    }

    /**
     * @param Widget $widget
     * @return WidgetTranslation
     * @throws Exception
     */
    public static function getDefaultWidgetTranslation(Widget $widget): WidgetTranslation
    {
        return self::getObjectWhere('`t`.`fk_widget_id` = ? AND `l`.`language_id` = ?', [$widget->getWidgetId(), $widget->getLanguage()->getLanguageId()]);
    }

    /**
     * @param Widget $widget
     * @param Language $language
     * @return WidgetTranslation
     * @throws Exception
     */
    public static function getWidgetTranslation(Widget $widget, Language $language): WidgetTranslation
    {
        $obj = self::getObjectWhere('`t`.`enabled` = 1 AND `t`.`fk_widget_id` = ? AND `l`.`language_id` = ?', [$widget->getWidgetId(), $language->getLanguageId()]);
        if ($obj->getWidgetTranslationId() > 0) {
            return $obj;
        }
        return self::getDefaultWidgetTranslation($widget);
    }

    /**
     * @return WidgetTranslation|null
     * @throws Exception
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
                $this->isEnabled() ? 1 : 0,
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
                $this->isEnabled() ? 1 : 0
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
        return $this->language ?: new Language();
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

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    /**
     * @param string $createdAt
     */
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    /**
     * @param string $updatedAt
     */
    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
