<?php

namespace GislerCMS\Model;

use Exception;
use PDO;

/**
 * Class WidgetTranslationHistory
 * @package GislerCMS\Model
 */
class WidgetTranslationHistory extends DbModel
{
    /**
     * @var int
     */
    private int $widgetTranslationHistoryId;

    /**
     * @var ?WidgetTranslation
     */
    private ?WidgetTranslation $widgetTranslation;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var bool
     */
    private bool $enabled;

    /**
     * @var ?User
     */
    private ?User $user;

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
    protected static string $table = 'cms__widget_translation_history';

    /**
     * WidgetTranslationHistory constructor.
     * @param int $widgetTranslationHistoryId
     * @param WidgetTranslation|null $widgetTranslation
     * @param string $content
     * @param bool $enabled
     * @param User|null $user
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int               $widgetTranslationHistoryId = 0,
        WidgetTranslation $widgetTranslation = null,
        string            $content = '',
        bool              $enabled = false,
        User              $user = null,
        string            $createdAt = '',
        string            $updatedAt = ''
    )
    {
        $this->widgetTranslationHistoryId = $widgetTranslationHistoryId;
        $this->widgetTranslation = $widgetTranslation;
        $this->content = $content;
        $this->enabled = $enabled;
        $this->user = $user;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return WidgetTranslationHistory[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`widget_translation_history_id`,
                `t`.`fk_widget_translation_id`,
                `t`.`content`,
                `t`.`enabled`,
                `t`.`created_at`,
                `t`.`updated_at`,
                `u`.`username`
            
            FROM `cms__widget_translation_history` `t`
            
            LEFT JOIN `cms__user` `u`
            ON `t`.`fk_user_id` = `u`.`user_id`
            
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
        ");
        $stmt->execute($args);
        $widgetTranslations = $stmt->fetchAll(PDO::FETCH_OBJ);
        if (sizeof($widgetTranslations) > 0) {
            foreach ($widgetTranslations as $widgetTranslation) {
                if ($widgetTranslation) {
                    $arr[] = new WidgetTranslationHistory(
                        $widgetTranslation->widget_translation_history_id,
                        WidgetTranslation::get($widgetTranslation->fk_widget_translation_id),
                        $widgetTranslation->content ?: '',
                        $widgetTranslation->enabled,
                        User::getByUsername($widgetTranslation->username ?: ''),
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
     * @return WidgetTranslationHistory
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): WidgetTranslationHistory
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new WidgetTranslationHistory();
    }

    /**
     * @param int $id
     * @return WidgetTranslationHistory
     * @throws Exception
     */
    public static function get(int $id): WidgetTranslationHistory
    {
        return self::getObjectWhere('`t`.`widget_translation_history_id` = ?', [$id]);
    }

    /**
     * @param WidgetTranslation $wt
     * @return array
     * @throws Exception
     */
    public static function getHistory(WidgetTranslation $wt): array
    {
        return self::getWhere('`t`.`fk_widget_translation_id` = ? ORDER BY `t`.`created_at` DESC', [$wt->getWidgetTranslationId()]);
    }

    /**
     * @return WidgetTranslationHistory|null
     * @throws Exception
     */
    public function save(): ?WidgetTranslationHistory
    {
        $pdo = self::getPDO();
        if ($this->getWidgetTranslationHistoryId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__widget_translation`
                SET `content` = ?, `enabled` = ?, `fk_user_id` = ?
                WHERE `widget_translation_history_id` = ?
            ");
            $res = $stmt->execute([
                $this->getContent(),
                $this->isEnabled() ? 1 : 0,
                $this->getUser()->getUserId(),
                $this->getWidgetTranslationHistoryId()
            ]);
            return $res ? self::get($this->getWidgetTranslationHistoryId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__widget_translation_history` (
                    `fk_widget_translation_id`, `content`, `enabled`, `fk_user_id`
                )
                VALUES (
                    ?, ?, ?, ?
                )
            ");
            $res = $stmt->execute([
                $this->getWidgetTranslation()->getWidgetTranslationId(),
                $this->getContent(),
                $this->isEnabled() ? 1 : 0,
                $this->getUser()->getUserId()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @return int
     */
    public function getWidgetTranslationHistoryId(): int
    {
        return $this->widgetTranslationHistoryId;
    }

    /**
     * @param int $widgetTranslationHistoryId
     */
    public function setWidgetTranslationHistoryId(int $widgetTranslationHistoryId): void
    {
        $this->widgetTranslationHistoryId = $widgetTranslationHistoryId;
    }

    /**
     * @return WidgetTranslation
     */
    public function getWidgetTranslation(): WidgetTranslation
    {
        return $this->widgetTranslation;
    }

    /**
     * @param WidgetTranslation $widgetTranslation
     */
    public function setWidgetTranslation(WidgetTranslation $widgetTranslation): void
    {
        $this->widgetTranslation = $widgetTranslation;
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
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
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
