<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use PDOStatement;

/**
 * Class Widget
 * @package GislerCMS\Model
 */
class Widget extends DbModel
{
    /**
     * @var int
     */
    private int $widgetId;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var bool
     */
    private bool $enabled;

    /**
     * @var bool
     */
    private bool $trash;

    /**
     * @var ?Language
     */
    private ?Language $language;

    /**
     * @var string
     */
    private string $createdAt;

    /**
     * @var string
     */
    private string $updatedAt;

    /**
     * @var string
     */
    protected static string $table = 'cms__widget';

    /**
     * Widget constructor.
     * @param int $widgetId
     * @param string $name
     * @param bool $enabled
     * @param bool $trash
     * @param Language|null $language
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int      $widgetId = 0,
        string   $name = '',
        bool     $enabled = true,
        bool     $trash = false,
        Language $language = null,
        string   $createdAt = '',
        string   $updatedAt = ''
    )
    {
        $this->widgetId = $widgetId;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->trash = $trash;
        $this->language = $language;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Widget[]
     * @throws Exception
     */
    private static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT
                `w`.`widget_id`,
                `w`.`name`,
                `w`.`enabled`,
                `w`.`trash`,
                `w`.`created_at`,
                `w`.`updated_at`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled',
                `l`.`created_at` AS 'l_created_at',
                `l`.`updated_at` AS 'l_updated_at'
            
            FROM `cms__widget` `w`
              
            INNER JOIN `cms__language` `l`
            ON `w`.fk_language_id = `l`.language_id
            
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
            
            ORDER BY `enabled` DESC, `name` ASC
        ");
        if ($stmt instanceof PDOStatement) {
            $stmt->execute($args);
            $widgets = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (sizeof($widgets) > 0) {
                foreach ($widgets as $widget) {
                    $arr[] = new Widget(
                        $widget->widget_id,
                        $widget->name,
                        $widget->enabled,
                        $widget->trash,
                        new Language(
                            $widget->language_id,
                            $widget->locale,
                            $widget->description,
                            $widget->l_enabled,
                            $widget->l_created_at,
                            $widget->l_updated_at
                        ),
                        $widget->created_at,
                        $widget->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Widget
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): Widget
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new Widget();
    }

    /**
     * @return Widget[]
     * @throws Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @return Widget[]
     * @throws Exception
     */
    public static function getAvailable(): array
    {
        return self::getWhere('`w`.`trash` = 0');
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function countAvailable(): int
    {
        return self::countWhere('`trash` = 0');
    }

    /**
     * @return Widget[]
     * @throws Exception
     */
    public static function getTrash(): array
    {
        return self::getWhere('`w`.`trash` = 1');
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function countTrash(): int
    {
        return self::countWhere('`trash` = 1');
    }

    /**
     * @param int $id
     * @return Widget
     * @throws Exception
     */
    public static function get(int $id): Widget
    {
        return self::getObjectWhere('`w`.`widget_id` = ?', [$id]);
    }

    /**
     * @param string $name
     * @return Widget
     * @throws Exception
     */
    public static function getWidget(string $name): Widget
    {
        return self::getObjectWhere('`w`.`name` = ?', [$name]);
    }

    /**
     * @return Widget|null
     * @throws Exception
     */
    public function save(): ?Widget
    {
        $pdo = self::getPDO();
        if ($this->getWidgetId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__widget`
                SET `name` = ?, `enabled` = ?, `trash` = ?, `fk_language_id` = ?
                WHERE `widget_id` = ?
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->isEnabled() ? 1 : 0,
                $this->isTrash() ? 1 : 0,
                $this->getLanguage()->getLanguageId(),
                $this->getWidgetId()
            ]);
            return $res ? self::get($this->getWidgetId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__widget` (`name`, `enabled`, `trash`, `fk_language_id`)
                VALUES (?, ?, ?, ?)
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->isEnabled() ? 1 : 0,
                $this->isTrash() ? 1 : 0,
                $this->getLanguage()->getLanguageId()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function delete(): bool
    {
        $pdo = self::getPDO();
        if ($this->getWidgetId() > 0) {
            $stmt = $pdo->prepare("
                DELETE FROM `cms__widget`
                WHERE `widget_id` = ?
            ");
            return $stmt->execute([$this->getWidgetId()]);
        }
        return false;
    }

    /**
     * @return Widget|null
     * @throws Exception
     */
    public function duplicate(): ?Widget
    {
        $dup = self::get($this->widgetId);
        $dup->setWidgetId(0);
        $dup->setName($this->name . ' (Copy)');
        $dup->setEnabled(false);
        $dup = $dup->save();
        if (!is_null($dup)) {
            /** @var WidgetTranslation $trans */
            foreach (self::getWidgetTranslations() as $trans) {
                $trans->setWidgetTranslationId(0);
                $trans->setWidget($dup);
                $tres = $trans->save();
                if (is_null($tres)) {
                    $dup->delete();
                    return null;
                }
            }
        } else {
            return null;
        }
        return $dup;
    }

    /**
     * @return WidgetTranslation
     * @throws Exception
     */
    public function getDefaultWidgetTranslation(): WidgetTranslation
    {
        return WidgetTranslation::getDefaultWidgetTranslation($this);
    }

    /**
     * @param Language $language
     * @return WidgetTranslation
     * @throws Exception
     */
    public function getWidgetTranslation(Language $language): WidgetTranslation
    {
        return WidgetTranslation::getWidgetTranslation($this, $language);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getWidgetTranslations(): array
    {
        return WidgetTranslation::getWidgetTranslations($this);
    }

    /**
     * @return int
     */
    public function getWidgetId(): int
    {
        return $this->widgetId;
    }

    /**
     * @param int $widgetId
     */
    public function setWidgetId(int $widgetId): void
    {
        $this->widgetId = $widgetId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     * @return bool
     */
    public function isTrash(): bool
    {
        return $this->trash;
    }

    /**
     * @param bool $trash
     */
    public function setTrash(bool $trash): void
    {
        $this->trash = $trash;
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
