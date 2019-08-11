<?php

namespace GislerCMS\Model;

/**
 * Class PageTranslation
 * @package GislerCMS\Model
 */
class PageTranslation extends DbModel
{
    /**
     * @var int
     */
    private $pageTranslationId;

    /**
     * @var Page
     */
    private $page;

    /**
     * @var Language
     */
    private $language;

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
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * PageTranslation constructor.
     * @param int $pageTranslationId
     * @param Page $page
     * @param Language $language
     * @param string $name
     * @param string $title
     * @param string $content
     * @param string $metaKeywords
     * @param string $metaDescription
     * @param string $metaAuthor
     * @param string $metaCopyright
     * @param string $metaImage
     * @param bool $enabled
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int $pageTranslationId = 0,
        Page $page = null,
        Language $language = null,
        string $name = '',
        string $title = '',
        string $content = '',
        string $metaKeywords = '',
        string $metaDescription = '',
        string $metaAuthor = '',
        string $metaCopyright = '',
        string $metaImage = '',
        bool $enabled = false,
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->pageTranslationId = $pageTranslationId;
        $this->page = $page;
        $this->language = $language;
        $this->name = $name;
        $this->title = $title;
        $this->content = $content;
        $this->metaKeywords = $metaKeywords;
        $this->metaDescription = $metaDescription;
        $this->metaAuthor = $metaAuthor;
        $this->metaCopyright = $metaCopyright;
        $this->metaImage = $metaImage;
        $this->enabled = $enabled;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param int $id
     * @return PageTranslation
     * @throws \Exception
     */
    public static function get(int $id): PageTranslation
    {
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`page_translation_id`,
                `t`.`fk_page_id`,
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
                `t`.`updated_at`
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled',
                `l`.`created_at` AS 'l_created_at',
                `l`.`updated_at` AS 'l_updated_at'
            
            FROM `cms__page_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            WHERE `t`.`page_translation_id` = ?
        ");
        $stmt->execute([$id]);
        $pageTranslation = $stmt->fetchObject();
        if ($pageTranslation) {
            return new PageTranslation(
                $pageTranslation->page_translation_id,
                Page::get($pageTranslation->fk_page_id),
                new Language(
                    $pageTranslation->language_id,
                    $pageTranslation->locale,
                    $pageTranslation->description,
                    $pageTranslation->l_enabled,
                    $pageTranslation->l_created_at,
                    $pageTranslation->l_updated_at
                ),
                $pageTranslation->name,
                $pageTranslation->title ?: '',
                $pageTranslation->content ?: '',
                $pageTranslation->meta_keywords ?: '',
                $pageTranslation->meta_description ?: '',
                $pageTranslation->meta_author ?: '',
                $pageTranslation->meta_copyright ?: '',
                $pageTranslation->meta_image ?: '',
                $pageTranslation->enabled,
                $pageTranslation->created_at,
                $pageTranslation->updated_at
            );
        }
        return new PageTranslation();
    }

    /**
     * @param Page $page
     * @return PageTranslation[]
     * @throws \Exception
     */
    public static function getPageTranslations(Page $page): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`page_translation_id`,
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
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled',
                `l`.`created_at` AS 'l_created_at',
                `l`.`updated_at` AS 'l_updated_at'
            
            FROM `cms__page_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            WHERE `t`.`fk_page_id` = ?
        ");
        $stmt->execute([$page->getPageId()]);
        $pageTranslations = $stmt->fetchAll(\PDO::FETCH_OBJ);
        if (sizeof($pageTranslations) > 0) {
            foreach ($pageTranslations as $pageTranslation) {
                $arr[$pageTranslation->locale] = new PageTranslation(
                    $pageTranslation->page_translation_id,
                    $page,
                    new Language(
                        $pageTranslation->language_id,
                        $pageTranslation->locale,
                        $pageTranslation->description,
                        $pageTranslation->l_enabled,
                        $pageTranslation->l_created_at,
                        $pageTranslation->l_updated_at
                    ),
                    $pageTranslation->name,
                    $pageTranslation->title ?: '',
                    $pageTranslation->content ?: '',
                    $pageTranslation->meta_keywords ?: '',
                    $pageTranslation->meta_description ?: '',
                    $pageTranslation->meta_author ?: '',
                    $pageTranslation->meta_copyright ?: '',
                    $pageTranslation->meta_image ?: '',
                    $pageTranslation->enabled,
                    $pageTranslation->created_at,
                    $pageTranslation->updated_at
                );
            }
        }
        return $arr;
    }

    /**
     * @param Page $page
     * @return PageTranslation
     * @throws \Exception
     */
    public static function getDefaultPageTranslation(Page $page): PageTranslation
    {
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`page_translation_id`,
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
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled',
                `l`.`created_at` AS 'l_created_at',
                `l`.`updated_at` AS 'l_updated_at'
            
            FROM `cms__page_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            WHERE `t`.`fk_page_id` = ?
            AND `l`.`language_id` = ?
        ");
        $stmt->execute([$page->getPageId(), $page->getLanguage()->getLanguageId()]);
        $pageTranslation = $stmt->fetchObject();
        if ($pageTranslation) {
            return new PageTranslation(
                $pageTranslation->page_translation_id,
                $page,
                new Language(
                    $pageTranslation->language_id,
                    $pageTranslation->locale,
                    $pageTranslation->description,
                    $pageTranslation->l_enabled,
                    $pageTranslation->l_created_at,
                    $pageTranslation->l_updated_at
                ),
                $pageTranslation->name,
                $pageTranslation->title ?: '',
                $pageTranslation->content ?: '',
                $pageTranslation->meta_keywords ?: '',
                $pageTranslation->meta_description ?: '',
                $pageTranslation->meta_author ?: '',
                $pageTranslation->meta_copyright ?: '',
                $pageTranslation->meta_image ?: '',
                $pageTranslation->enabled,
                $pageTranslation->created_at,
                $pageTranslation->updated_at
            );
        }
        return new PageTranslation();
    }

    /**
     * @param Page $page
     * @param Language $language
     * @return PageTranslation
     * @throws \Exception
     */
    public static function getPageTranslation(Page $page, Language $language): PageTranslation
    {
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`page_translation_id`,
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
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled',
                `l`.`created_at` AS 'l_created_at',
                `l`.`updated_at` AS 'l_updated_at'
            
            FROM `cms__page_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            WHERE `t`.`enabled` = 1
            AND `t`.`fk_page_id` = ?
            AND `l`.`language_id` = ?
        ");
        $stmt->execute([$page->getPageId(), $language->getLanguageId()]);
        $pageTranslation = $stmt->fetchObject();
        if ($pageTranslation) {
            return new PageTranslation(
                $pageTranslation->page_translation_id,
                $page,
                new Language(
                    $pageTranslation->language_id,
                    $pageTranslation->locale,
                    $pageTranslation->description,
                    $pageTranslation->l_enabled,
                    $pageTranslation->l_created_at,
                    $pageTranslation->l_updated_at
                ),
                $pageTranslation->name,
                $pageTranslation->title ?: '',
                $pageTranslation->content ?: '',
                $pageTranslation->meta_keywords ?: '',
                $pageTranslation->meta_description ?: '',
                $pageTranslation->meta_author ?: '',
                $pageTranslation->meta_copyright ?: '',
                $pageTranslation->meta_image ?: '',
                $pageTranslation->enabled,
                $pageTranslation->created_at,
                $pageTranslation->updated_at
            );
        }
        return self::getDefaultPageTranslation($page);
    }

    /**
     * @return PageTranslation|null
     * @throws \Exception
     */
    public function save(): ?PageTranslation
    {
        $pdo = self::getPDO();
        if ($this->getPageTranslationId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__page_translation`
                SET `name` = ?, `title` = ?, `content` = ?, `meta_keywords` = ?, `meta_description` = ?,
                    `meta_author` = ?, `meta_copyright` = ?, `meta_image` = ?, `enabled` = ?
                WHERE `page_translation_id` = ?
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
                $this->getPageTranslationId()
            ]);
            return $res ? self::get($this->getPageTranslationId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__page_translation` (
                    `fk_page_id`, `fk_language_id`, `name`, `title`, `content`, `meta_keywords`,
                    `meta_description`, `meta_author`, `meta_copyright`, `meta_image`, `enabled`
                )
                VALUES (
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?
                )
            ");
            $res = $stmt->execute([
                $this->getPage()->getPageId(),
                $this->getLanguage()->getLanguageId(),
                $this->getName(),
                $this->getTitle(),
                $this->getContent(),
                $this->getMetaKeywords(),
                $this->getMetaDescription(),
                $this->getMetaAuthor(),
                $this->getMetaCopyright(),
                $this->getMetaImage(),
                $this->isEnabled()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @return int
     */
    public function getPageTranslationId(): int
    {
        return $this->pageTranslationId;
    }

    /**
     * @param int $pageTranslationId
     */
    public function setPageTranslationId(int $pageTranslationId): void
    {
        $this->pageTranslationId = $pageTranslationId;
    }

    /**
     * @return Page
     */
    public function getPage(): Page
    {
        return $this->page;
    }

    /**
     * @param Page $page
     */
    public function setPage(Page $page): void
    {
        $this->page = $page;
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
