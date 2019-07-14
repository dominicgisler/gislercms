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
        bool $enabled = false
    ) {
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
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled'
            
            FROM `cms__page_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            WHERE `t`.`fk_page_id` = ?
        ");
        $stmt->execute([$page->getPageId()]);
        $pageTranslations =  $stmt->fetchAll(\PDO::FETCH_OBJ);
        if (sizeof($pageTranslations) > 0) {
            foreach ($pageTranslations as $pageTranslation) {
                $arr[$pageTranslation->locale] = new PageTranslation(
                    $pageTranslation->page_translation_id,
                    $page,
                    new Language(
                        $pageTranslation->language_id,
                        $pageTranslation->locale,
                        $pageTranslation->description,
                        $pageTranslation->l_enabled
                    ),
                    $pageTranslation->name,
                    $pageTranslation->title ?: '',
                    $pageTranslation->content ?: '',
                    $pageTranslation->meta_keywords ?: '',
                    $pageTranslation->meta_description ?: '',
                    $pageTranslation->meta_author ?: '',
                    $pageTranslation->meta_copyright ?: '',
                    $pageTranslation->meta_image ?: '',
                    $pageTranslation->enabled
                );
            }
        }
        return $arr;
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
}
