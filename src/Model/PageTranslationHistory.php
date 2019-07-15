<?php

namespace GislerCMS\Model;

/**
 * Class PageTranslationHistory
 * @package GislerCMS\Model
 */
class PageTranslationHistory extends DbModel
{
    /**
     * @var int
     */
    private $pageTranslationHistoryId;

    /**
     * @var PageTranslation
     */
    private $pageTranslation;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $content;

    /**
     * @var string
     */
    private $metaKeywords;

    /**
     * @var string
     */
    private $metaDescription;

    /**
     * @var string
     */
    private $metaAuthor;

    /**
     * @var string
     */
    private $metaCopyright;

    /**
     * @var string
     */
    private $metaImage;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var User
     */
    private $user;

    /**
     * PageTranslationHistory constructor.
     * @param int $pageTranslationHistoryId
     * @param PageTranslation $pageTranslation
     * @param string $name
     * @param string $title
     * @param string $content
     * @param string $metaKeywords
     * @param string $metaDescription
     * @param string $metaAuthor
     * @param string $metaCopyright
     * @param string $metaImage
     * @param bool $enabled
     * @param User $user
     */
    public function __construct(
        int $pageTranslationHistoryId = 0,
        PageTranslation $pageTranslation = null,
        string $name = '',
        string $title = '',
        string $content = '',
        string $metaKeywords = '',
        string $metaDescription = '',
        string $metaAuthor = '',
        string $metaCopyright = '',
        string $metaImage = '',
        bool $enabled = false,
        User $user = null
    ) {
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
    }

    /**
     * @param int $id
     * @return PageTranslationHistory
     * @throws \Exception
     */
    public static function get(int $id): PageTranslationHistory
    {
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
                `u`.`username`
            
            FROM `cms__page_translation_history` `t`
            
            INNER JOIN `cms__user` `u`
            ON `t`.`fk_user_id` = `u`.`user_id`
            
            WHERE `t`.`page_translation_history_id` = ?
        ");
        $stmt->execute([$id]);
        $pageTranslation = $stmt->fetchObject();
        if ($pageTranslation) {
            return new PageTranslationHistory(
                $pageTranslation->page_translation_history_id,
                PageTranslation::get($pageTranslation->fk_page_translation_id),
                $pageTranslation->name,
                $pageTranslation->title ?: '',
                $pageTranslation->content ?: '',
                $pageTranslation->meta_keywords ?: '',
                $pageTranslation->meta_description ?: '',
                $pageTranslation->meta_author ?: '',
                $pageTranslation->meta_copyright ?: '',
                $pageTranslation->meta_image ?: '',
                $pageTranslation->enabled,
                User::getByUsername($pageTranslation->username)
            );
        }
        return new PageTranslationHistory();
    }

    /**
     * @return PageTranslationHistory|null
     * @throws \Exception
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
                $this->isEnabled(),
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
                $this->isEnabled(),
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
}
