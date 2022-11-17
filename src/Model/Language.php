<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use PDOStatement;

/**
 * Class Language
 * @package GislerCMS\Model
 */
class Language extends DbModel
{
    /**
     * @var int
     */
    private $languageId;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     */
    private $description;

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
     * @var string
     */
    protected static $table = 'cms__language';

    /**
     * Language constructor.
     * @param int $languageId
     * @param string $locale
     * @param string $description
     * @param bool $enabled
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int    $languageId = 0,
        string $locale = '',
        string $description = '',
        bool   $enabled = true,
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->languageId = $languageId;
        $this->locale = $locale;
        $this->description = $description;
        $this->enabled = $enabled;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @param string $orderBy
     * @return Language[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = [], string $orderBy = ''): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__language` " . (!empty($where) ? 'WHERE ' . $where : '') . (!empty($orderBy) ? 'ORDER BY ' . $orderBy : ''));
        if ($stmt instanceof PDOStatement) {
            $stmt->execute($args);
            $languages = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (sizeof($languages) > 0) {
                foreach ($languages as $language) {
                    $arr[] = new Language(
                        $language->language_id,
                        $language->locale,
                        $language->description,
                        $language->enabled,
                        $language->created_at,
                        $language->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Language
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): Language
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new Language();
    }

    /**
     * @return Language[]
     * @throws Exception
     */
    public static function getAll(): array
    {
        return self::getWhere('', [], '`description` ASC');
    }

    /**
     * @param string $locale
     * @return Language
     * @throws Exception
     */
    public static function getLanguage(string $locale): Language
    {
        return self::getObjectWhere('`locale` = ? AND `enabled` = 1', [$locale]);
    }

    /**
     * @param int $id
     * @return Language
     * @throws Exception
     */
    public static function get(int $id): Language
    {
        return self::getObjectWhere('`language_id` = ?', [$id]);
    }

    /**
     * @return Language|null
     * @throws Exception
     */
    public function save(): ?Language
    {
        $pdo = self::getPDO();
        if ($this->getLanguageId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__language`
                SET `locale` = ?, `description` = ?, `enabled` = ?
                WHERE `language_id` = ?
            ");
            $res = $stmt->execute([
                $this->getLocale(),
                $this->getDescription(),
                $this->isEnabled() ? 1 : 0,
                $this->getLanguageId()
            ]);
            return $res ? self::get($this->getLanguageId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__language` (`locale`, `description`, `enabled`)
                VALUES (?, ?, ?)
            ");
            $res = $stmt->execute([
                $this->getLocale(),
                $this->getDescription(),
                $this->isEnabled() ? 1 : 0
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function delete(): bool
    {
        $pdo = self::getPDO();
        if ($this->getLanguageId() > 0) {
            $stmt = $pdo->prepare("
                DELETE FROM `cms__language`
                WHERE `language_id` = ?
            ");
            return $stmt->execute([$this->getLanguageId()]);
        }
        return false;
    }

    /**
     * @return int
     */
    public function getLanguageId(): int
    {
        return $this->languageId;
    }

    /**
     * @param int $languageId
     */
    public function setLanguageId(int $languageId): void
    {
        $this->languageId = $languageId;
    }

    /**
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
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
