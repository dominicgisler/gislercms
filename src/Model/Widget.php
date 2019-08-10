<?php

namespace GislerCMS\Model;

/**
 * Class Widget
 * @package GislerCMS\Model
 */
class Widget extends DbModel
{
    /**
     * @var int
     */
    private $widgetId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var bool
     */
    private $trash;

    /**
     * @var Language
     */
    private $language;

    /**
     * Widget constructor.
     * @param int $widgetId
     * @param string $name
     * @param bool $enabled
     * @param bool $trash
     * @param Language $language
     */
    public function __construct(int $widgetId = 0, string $name = '', bool $enabled = true, bool $trash = false, Language $language = null)
    {
        $this->widgetId = $widgetId;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->trash = $trash;
        $this->language = $language;
    }

    /**
     * @return Widget[]
     * @throws \Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @return Widget[]
     * @throws \Exception
     */
    public static function getTrash(): array
    {
        return self::getWhere('`p`.`trash` = 1');
    }

    /**
     * @param string $where
     * @return Widget[]
     * @throws \Exception
     */
    private static function getWhere($where = ''): array
    {
        $arr = [];
        $stmt = self::getPDO()->query("
            SELECT
                `p`.`widget_id`,
                `p`.`name`,
                `p`.`enabled`,
                `p`.`trash`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled'
            
            FROM `cms__widget` `p`
              
            INNER JOIN `cms__language` `l`
            ON `p`.fk_language_id = `l`.language_id
            
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
            
            ORDER BY `enabled` DESC, `name` ASC
        ");
        if ($stmt instanceof \PDOStatement) {
            $widgets = $stmt->fetchAll(\PDO::FETCH_OBJ);
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
                            $widget->l_enabled
                        )
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param int $id
     * @return Widget
     * @throws \Exception
     */
    public static function get(int $id): Widget
    {
        $stmt = self::getPDO()->prepare("
            SELECT
                `p`.`widget_id`,
                `p`.`name`,
                `p`.`enabled`,
                `p`.`trash`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled'
            
            FROM `cms__widget` `p`
              
            INNER JOIN `cms__language` `l`
            ON `p`.fk_language_id = `l`.language_id
            
            WHERE `p`.`widget_id` = ?
        ");
        $stmt->execute([$id]);
        $widget = $stmt->fetchObject();
        if ($widget) {
            return new Widget(
                $widget->widget_id,
                $widget->name,
                $widget->enabled,
                $widget->trash,
                new Language(
                    $widget->language_id,
                    $widget->locale,
                    $widget->description,
                    $widget->l_enabled
                )
            );
        }
        return new Widget();
    }

    /**
     * @param string $name
     * @param Language $language
     * @return Widget
     * @throws \Exception
     */
    public static function getWidgetWithLanguage(string $name, Language $language): Widget
    {
        $stmt = self::getPDO()->prepare("
            SELECT
                `w`.`widget_id`,
                `w`.`name`,
                `w`.`enabled`,
                `w`.`trash`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled'
            
            FROM `cms__widget` `w`
              
            INNER JOIN `cms__language` `l`
            ON `w`.fk_language_id = `l`.language_id
            
            WHERE `w`.`name` = ?
            AND `l`.`language_id` = ?
        ");
        $stmt->execute([$name, $language->getLanguageId()]);
        $widget = $stmt->fetchObject();
        if ($widget) {
            return new Widget(
                $widget->widget_id,
                $widget->name,
                $widget->enabled,
                $widget->trash,
                new Language(
                    $widget->language_id,
                    $widget->locale,
                    $widget->description,
                    $widget->l_enabled
                )
            );
        }
        return new Widget();
    }

    /**
     * @return Widget|null
     * @throws \Exception
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
                $this->isEnabled(),
                $this->isTrash(),
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
                $this->isEnabled(),
                $this->isTrash(),
                $this->getLanguage()->getLanguageId()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @return bool
     * @throws \Exception
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
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language): void
    {
        $this->language = $language;
    }
}
