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
                `l`.`enabled` AS 'l_enabled'
            
            FROM `cms__page` `p`
              
            INNER JOIN `cms__language` `l`
            ON `p`.fk_language_id = `l`.language_id
            
            ORDER BY `enabled` DESC, `name` ASC
        ");
        if ($stmt instanceof \PDOStatement) {
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
                            $page->description,
                            $page->l_enabled
                        )
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param int $id
     * @return Page
     * @throws \Exception
     */
    public static function get(int $id): Page
    {
        $stmt = self::getPDO()->prepare("
            SELECT
                `p`.`page_id`,
                `p`.`name`,
                `p`.`enabled`,
                `p`.`trash`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled'
            
            FROM `cms__page` `p`
              
            INNER JOIN `cms__language` `l`
            ON `p`.fk_language_id = `l`.language_id
            
            WHERE `p`.`page_id` = ?
        ");
        $stmt->execute([$id]);
        $page = $stmt->fetchObject();
        if ($page) {
            return new Page(
                $page->page_id,
                $page->name,
                $page->enabled,
                $page->trash,
                new Language(
                    $page->language_id,
                    $page->locale,
                    $page->description,
                    $page->l_enabled
                )
            );
        }
        return new Page();
    }

    /**
     * @return Page|null
     * @throws \Exception
     */
    public function save(): ?Page
    {
        $pdo = self::getPDO();
        if ($this->getPageId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__page`
                SET `name` = ?, `enabled` = ?, `trash` = ?, `fk_language_id` = ?
                WHERE `page_id` = ?
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->isEnabled(),
                $this->isTrash(),
                $this->getLanguage()->getLanguageId(),
                $this->getPageId()
            ]);
            return $res ? self::get($this->getPageId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__page` (`name`, `enabled`, `trash`, `fk_language_id`)
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
