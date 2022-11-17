<?php

namespace GislerCMS\Model;

use Exception;
use PDO;

/**
 * Class PageTranslationHistory
 * @package GislerCMS\Model
 */
class PageTranslationHistory extends DbModel
{
    /**
     * @var int
     */
    private int $pageTranslationHistoryId;

    /**
     * @var ?PageTranslation
     */
    private ?PageTranslation $pageTranslation;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var string
     */
    private string $metaKeywords;

    /**
     * @var string
     */
    private string $metaDescription;

    /**
     * @var string
     */
    private string $metaAuthor;

    /**
     * @var string
     */
    private string $metaCopyright;

    /**
     * @var string
     */
    private string $metaImage;

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
    protected static string $table = 'cms__page_translation_history';

    /**
     * PageTranslationHistory constructor.
     * @param int $pageTranslationHistoryId
     * @param PageTranslation|null $pageTranslation
     * @param string $name
     * @param string $title
     * @param string $content
     * @param string $metaKeywords
     * @param string $metaDescription
     * @param string $metaAuthor
     * @param string $metaCopyright
     * @param string $metaImage
     * @param bool $enabled
     * @param User|null $user
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int             $pageTranslationHistoryId = 0,
        PageTranslation $pageTranslation = null,
        string          $name = '',
        string          $title = '',
        string          $content = '',
        string          $metaKeywords = '',
        string          $metaDescription = '',
        string          $metaAuthor = '',
        string          $metaCopyright = '',
        string          $metaImage = '',
        bool            $enabled = false,
        User            $user = null,
        string          $createdAt = '',
        string          $updatedAt = ''
    )
    {
        $this->pageTranslationHistoryId = $pageTranslationHistoryId;
        $this->pageTranslation = $pageTranslation;
        $this->name = $name;
        $this->title = $title;
        $this->content = $content;
        $this->metaKeywords = $metaKeywords;
        $this->metaDescription = $metaDescription;
        $this->metaAuthor = $metaAuthor;
        $this->metaCopyright = $metaCopyright;
        $this->metaImage = $metaImage;
        $this->enabled = $enabled;
        $this->user = $user;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return PageTranslationHistory[]
     * @throws Exception
     */
    private static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`page_translation_history_id`,
                `t`.`fk_page_translation_id`,
                `t`.`name`,
                `t`.`title`,
                `t`.`content`,
                `t`.`meta_keywords`,
                `t`.`meta_description`,
                `t`.`meta_author`,
                `t`.`meta_copyright`,
                `t`.`meta_image`,
                `t`.`enabled`,
                `t`.`created_at`,
                `t`.`updated_at`,
                `u`.`user_id` AS 'u_user_id',
                `u`.`username` AS 'u_username',
                `u`.`firstname` AS 'u_firstname',
                `u`.`lastname` AS 'u_lastname',
                `u`.`email` AS 'u_email',
                `u`.`locale` AS 'u_locale',
                `u`.`failed_logins` AS 'u_failed_logins',
                `u`.`locked` AS 'u_locked',
                `u`.`last_login` AS 'u_last_login',
                `u`.`last_activity` AS 'u_last_activity',
                `u`.`created_at` AS 'u_created_at',
                `u`.`updated_at` AS 'u_updated_at'
            
            FROM `cms__page_translation_history` `t`
            
            LEFT JOIN `cms__user` `u`
            ON `t`.`fk_user_id` = `u`.`user_id`
            
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
        ");
        $stmt->execute($args);
        $pageTranslations = $stmt->fetchAll(PDO::FETCH_OBJ);
        if (sizeof($pageTranslations) > 0) {
            foreach ($pageTranslations as $pageTranslation) {
                $arr[] = new PageTranslationHistory(
                    $pageTranslation->page_translation_history_id,
                    new PageTranslation(),
                    $pageTranslation->name,
                    $pageTranslation->title ?: '',
                    $pageTranslation->content ?: '',
                    $pageTranslation->meta_keywords ?: '',
                    $pageTranslation->meta_description ?: '',
                    $pageTranslation->meta_author ?: '',
                    $pageTranslation->meta_copyright ?: '',
                    $pageTranslation->meta_image ?: '',
                    $pageTranslation->enabled,
                    new User(
                        $pageTranslation->u_user_id ?: 0,
                        $pageTranslation->u_username ?: '',
                        $pageTranslation->u_firstname ?: '',
                        $pageTranslation->u_lastname ?: '',
                        $pageTranslation->u_email ?: '',
                        '',
                        $pageTranslation->u_locale ?: '',
                        $pageTranslation->u_failed_logins ?: 0,
                        $pageTranslation->u_locked ?: false,
                        '',
                        $pageTranslation->u_last_login ?: '',
                        $pageTranslation->u_last_activity ?: '',
                        $pageTranslation->u_created_at ?: '',
                        $pageTranslation->u_updated_at ?: ''
                    ),
                    $pageTranslation->created_at,
                    $pageTranslation->updated_at
                );
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return PageTranslationHistory
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): PageTranslationHistory
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new PageTranslationHistory();
    }

    /**
     * @param int $id
     * @return PageTranslationHistory
     * @throws Exception
     */
    public static function get(int $id): PageTranslationHistory
    {
        return self::getObjectWhere('`t`.`page_translation_history_id` = ?', [$id]);
    }

    /**
     * @param PageTranslation $pt
     * @return PageTranslationHistory[]
     * @throws Exception
     */
    public static function getHistory(PageTranslation $pt): array
    {
        return self::getWhere('`t`.`fk_page_translation_id` = ? ORDER BY `t`.`created_at` DESC', [$pt->getPageTranslationId()]);
    }

    /**
     * @return PageTranslationHistory|null
     * @throws Exception
     */
    public function save(): ?PageTranslationHistory
    {
        $pdo = self::getPDO();
        if ($this->getPageTranslationHistoryId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__page_translation`
                SET `name` = ?, `title` = ?, `content` = ?, `meta_keywords` = ?, `meta_description` = ?,
                    `meta_author` = ?, `meta_copyright` = ?, `meta_image` = ?, `enabled` = ?, `fk_user_id` = ?
                WHERE `page_translation_history_id` = ?
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->getTitle(),
                $this->getContent(),
                $this->getMetaKeywords(),
                $this->getMetaDescription(),
                $this->getMetaAuthor(),
                $this->getMetaCopyright(),
                $this->getMetaImage(),
                $this->isEnabled() ? 1 : 0,
                $this->getUser()->getUserId(),
                $this->getPageTranslationHistoryId()
            ]);
            return $res ? self::get($this->getPageTranslationHistoryId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__page_translation_history` (
                    `fk_page_translation_id`, `name`, `title`, `content`, `meta_keywords`, `meta_description`,
                    `meta_author`, `meta_copyright`, `meta_image`, `enabled`, `fk_user_id`
                )
                VALUES (
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?
                )
            ");
            $res = $stmt->execute([
                $this->getPageTranslation()->getPageTranslationId(),
                $this->getName(),
                $this->getTitle(),
                $this->getContent(),
                $this->getMetaKeywords(),
                $this->getMetaDescription(),
                $this->getMetaAuthor(),
                $this->getMetaCopyright(),
                $this->getMetaImage(),
                $this->isEnabled() ? 1 : 0,
                $this->getUser()->getUserId()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @return int
     */
    public function getPageTranslationHistoryId(): int
    {
        return $this->pageTranslationHistoryId;
    }

    /**
     * @param int $pageTranslationHistoryId
     */
    public function setPageTranslationHistoryId(int $pageTranslationHistoryId): void
    {
        $this->pageTranslationHistoryId = $pageTranslationHistoryId;
    }

    /**
     * @return PageTranslation
     */
    public function getPageTranslation(): PageTranslation
    {
        return $this->pageTranslation;
    }

    /**
     * @param PageTranslation $pageTranslation
     */
    public function setPageTranslation(PageTranslation $pageTranslation): void
    {
        $this->pageTranslation = $pageTranslation;
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
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
     * @return string
     */
    public function getMetaKeywords(): string
    {
        return $this->metaKeywords;
    }

    /**
     * @param string $metaKeywords
     */
    public function setMetaKeywords(string $metaKeywords): void
    {
        $this->metaKeywords = $metaKeywords;
    }

    /**
     * @return string
     */
    public function getMetaDescription(): string
    {
        return $this->metaDescription;
    }

    /**
     * @param string $metaDescription
     */
    public function setMetaDescription(string $metaDescription): void
    {
        $this->metaDescription = $metaDescription;
    }

    /**
     * @return string
     */
    public function getMetaAuthor(): string
    {
        return $this->metaAuthor;
    }

    /**
     * @param string $metaAuthor
     */
    public function setMetaAuthor(string $metaAuthor): void
    {
        $this->metaAuthor = $metaAuthor;
    }

    /**
     * @return string
     */
    public function getMetaCopyright(): string
    {
        return $this->metaCopyright;
    }

    /**
     * @param string $metaCopyright
     */
    public function setMetaCopyright(string $metaCopyright): void
    {
        $this->metaCopyright = $metaCopyright;
    }

    /**
     * @return string
     */
    public function getMetaImage(): string
    {
        return $this->metaImage;
    }

    /**
     * @param string $metaImage
     */
    public function setMetaImage(string $metaImage): void
    {
        $this->metaImage = $metaImage;
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
