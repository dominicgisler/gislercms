<?php

namespace GislerCMS\Model;

/**
 * Class Page
 * @package GislerCMS\Model
 */
class Page extends DbModel
{
    /**
     * @var int
     */
    private $pageId;

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
     * Page constructor.
     * @param int $pageId
     * @param string $name
     * @param bool $enabled
     * @param bool $trash
     * @param Language $language
     */
    public function __construct(int $pageId = 0, string $name = '', bool $enabled = true, bool $trash = false, Language $language = null)
    {
        $this->pageId = $pageId;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->trash = $trash;
        $this->language = $language;
    }

    /**
     * @return Page[]
     * @throws \Exception
     */
    public static function getAll(): array
    {
        $arr = [];
        $stmt = self::getPDO()->query("
            SELECT
                `p`.`page_id`,
                `p`.`name`,
                `p`.`enabled`,
                `p`.`trash`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled`
            
            FROM `cms__page` `p`
              
            INNER JOIN `cms__language` `l`
            ON `p`.fk_language_id = `l`.language_id
            
            ORDER BY `name` ASC
        ");
        $pages = $stmt->fetchAll(\PDO::FETCH_OBJ);
        if (sizeof($pages) > 0) {
            foreach ($pages as $page) {
                $arr[] = new Page(
                    $page->page_id,
                    $page->name,
                    $page->enabled,
                    $page->trash,
                    new Language(
                        $page->language_id,
                        $page->locale,
                        $page->description
                    )
                );
            }
        }
        return $arr;
    }

    /**
     * @return int
     */
    public function getPageId(): int
    {
        return $this->pageId;
    }

    /**
     * @param int $pageId
     */
    public function setPageId(int $pageId): void
    {
        $this->pageId = $pageId;
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
