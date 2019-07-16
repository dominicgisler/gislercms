<?php

namespace GislerCMS\Model;

/**
 * Class WidgetTranslationHistory
 * @package GislerCMS\Model
 */
class WidgetTranslationHistory extends DbModel
{
    /**
     * @var int
     */
    private $widgetTranslationHistoryId;

    /**
     * @var WidgetTranslation
     */
    private $widgetTranslation;

    /**
     * @var string
     */
    private $content;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var User
     */
    private $user;

    /**
     * WidgetTranslationHistory constructor.
     * @param int $widgetTranslationHistoryId
     * @param WidgetTranslation $widgetTranslation
     * @param string $content
     * @param bool $enabled
     * @param User $user
     */
    public function __construct(
        int $widgetTranslationHistoryId = 0,
        WidgetTranslation $widgetTranslation = null,
        string $content = '',
        bool $enabled = false,
        User $user = null
    ) {
        $this->widgetTranslationHistoryId = $widgetTranslationHistoryId;
        $this->widgetTranslation = $widgetTranslation;
        $this->content = $content;
        $this->enabled = $enabled;
        $this->user = $user;
    }

    /**
     * @param int $id
     * @return WidgetTranslationHistory
     * @throws \Exception
     */
    public static function get(int $id): WidgetTranslationHistory
    {
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`widget_translation_history_id`,
                `t`.`fk_widget_translation_id`,
                `t`.`content`,
                `t`.`enabled`,
                `u`.`username`
            
            FROM `cms__widget_translation_history` `t`
            
            INNER JOIN `cms__user` `u`
            ON `t`.`fk_user_id` = `u`.`user_id`
            
            WHERE `t`.`widget_translation_history_id` = ?
        ");
        $stmt->execute([$id]);
        $widgetTranslation = $stmt->fetchObject();
        if ($widgetTranslation) {
            return new WidgetTranslationHistory(
                $widgetTranslation->widget_translation_history_id,
                WidgetTranslation::get($widgetTranslation->fk_widget_translation_id),
                $widgetTranslation->content ?: '',
                $widgetTranslation->enabled,
                User::getByUsername($widgetTranslation->username)
            );
        }
        return new WidgetTranslationHistory();
    }

    /**
     * @return WidgetTranslationHistory|null
     * @throws \Exception
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
                $this->isEnabled(),
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
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?
                )
            ");
            $res = $stmt->execute([
                $this->getWidgetTranslation()->getWidgetTranslationId(),
                $this->getContent(),
                $this->isEnabled(),
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
}
