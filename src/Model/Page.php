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
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * Page constructor.
     * @param int $pageId
     * @param string $name
     * @param bool $enabled
     * @param bool $trash
     * @param Language $language
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int $pageId = 0,
        string $name = '',
        bool $enabled = true,
        bool $trash = false,
        Language $language = null,
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->pageId = $pageId;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->trash = $trash;
        $this->language = $language;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return Page[]
     * @throws \Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @return Page[]
     * @throws \Exception
     */
    public static function getAvailable(): array
    {
        return self::getWhere('`p`.`trash` = 0');
    }

    /**
     * @return Page[]
     * @throws \Exception
     */
    public static function getTrash(): array
    {
        return self::getWhere('`p`.`trash` = 1');
    }

    /**
     * @param string $where
     * @return Page[]
     * @throws \Exception
     */
    public static function getWhere($where = ''): array
    {
        $arr = [];
        $stmt = self::getPDO()->query("
            SELECT
                `p`.`page_id`,
                `p`.`name`,
                `p`.`enabled`,
                `p`.`trash`,
                `p`.`created_at`,
                `p`.`updated_at`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled',
                `l`.`created_at` AS 'l_created_at',
                `l`.`updated_at` AS 'l_updated_at'
            
            FROM `cms__page` `p`
              
            INNER JOIN `cms__language` `l`
            ON `p`.fk_language_id = `l`.language_id
            
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
            
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
                            $page->l_enabled,
                            $page->l_created_at,
                            $page->l_updated_at
                        ),
                        $page->created_at,
                        $page->updated_at
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
                `p`.`created_at`,
                `p`.`updated_at`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled',
                `l`.`created_at` AS 'l_created_at',
                `l`.`updated_at` AS 'l_updated_at'
            
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
                    $page->l_enabled,
                    $page->l_created_at,
                    $page->l_updated_at
                ),
                $page->created_at,
                $page->updated_at
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
                $this->isEnabled() ? 1 : 0,
                $this->isTrash() ? 1 : 0,
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
                $this->isEnabled() ? 1 : 0,
                $this->isTrash() ? 1 : 0,
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
        if ($this->getPageId() > 0) {
            $stmt = $pdo->prepare("
                DELETE FROM `cms__page`
                WHERE `page_id` = ?
            ");
            return $stmt->execute([$this->getPageId()]);
        }
        return false;
    }

    /**
     * @return PageTranslation
     * @throws \Exception
     */
    public function getDefaultPageTranslation()
    {
        return PageTranslation::getDefaultPageTranslation($this);
    }

    /**
     * @param Language $language
     * @return PageTranslation
     * @throws \Exception
     */
    public function getPageTranslation(Language $language)
    {
        return PageTranslation::getPageTranslation($this, $language);
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
