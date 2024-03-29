<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use PDOStatement;

/**
 * Class Post
 * @package GislerCMS\Model
 */
class Post extends DbModel
{
    /**
     * @var int
     */
    private int $postId;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var bool
     */
    private bool $enabled;

    /**
     * @var bool
     */
    private bool $trash;

    /**
     * @var ?Language
     */
    private ?Language $language;

    /**
     * @var string
     */
    private string $publishAt;

    /**
     * @var string[]
     */
    private array $categories;

    /**
     * @var PostAttribute[]
     */
    private array $attributes;

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
    protected static string $table = 'cms__post';

    /**
     * Post constructor.
     * @param int $postId
     * @param string $name
     * @param bool $enabled
     * @param bool $trash
     * @param Language|null $language
     * @param string $publishAt
     * @param string[] $categories
     * @param array $attributes
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int      $postId = 0,
        string   $name = '',
        bool     $enabled = true,
        bool     $trash = false,
        Language $language = null,
        string   $publishAt = '',
        array    $categories = [],
        array    $attributes = [],
        string   $createdAt = '',
        string   $updatedAt = ''
    )
    {
        $this->postId = $postId;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->trash = $trash;
        $this->language = $language;
        $this->publishAt = $publishAt;
        $this->categories = $categories;
        $this->attributes = $attributes;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Post[]
     * @throws Exception
     */
    private static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT
                `p`.`post_id`,
                `p`.`name`,
                `p`.`enabled`,
                `p`.`trash`,
                `p`.`categories`,
                `p`.`publish_at`,
                `p`.`created_at`,
                `p`.`updated_at`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled',
                `l`.`created_at` AS 'l_created_at',
                `l`.`updated_at` AS 'l_updated_at'
            
            FROM `cms__post` `p`
              
            INNER JOIN `cms__language` `l`
            ON `p`.fk_language_id = `l`.language_id
            
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
            
            ORDER BY `enabled` DESC, `p`.`publish_at` DESC, `name` ASC
        ");
        if ($stmt instanceof PDOStatement) {
            $stmt->execute($args);
            $posts = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (sizeof($posts) > 0) {
                foreach ($posts as $post) {
                    $cats = explode("\0", $post->categories);
                    if (sizeof($cats) == 1 && $cats[0] == '') {
                        $cats = [];
                    }
                    $arr[] = new Post(
                        $post->post_id,
                        $post->name,
                        $post->enabled,
                        $post->trash,
                        new Language(
                            $post->language_id,
                            $post->locale,
                            $post->description,
                            $post->l_enabled,
                            $post->l_created_at,
                            $post->l_updated_at
                        ),
                        $post->publish_at,
                        $cats,
                        PostAttribute::getByPostId($post->post_id),
                        $post->created_at,
                        $post->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Post
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): Post
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new Post();
    }

    /**
     * @return Post[]
     * @throws Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @return Post[]
     * @throws Exception
     */
    public static function getAvailable(): array
    {
        return self::getWhere('`p`.`trash` = 0');
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function countAvailable(): int
    {
        return self::countWhere('`trash` = 0');
    }

    /**
     * @return Post[]
     * @throws Exception
     */
    public static function getTrash(): array
    {
        return self::getWhere('`p`.`trash` = 1');
    }

    /**
     * @return int
     * @throws Exception
     */
    public static function countTrash(): int
    {
        return self::countWhere('`trash` = 1');
    }

    /**
     * @param string $name
     * @return Post[]
     * @throws Exception
     */
    public static function getByCategory(string $name): array
    {
        return self::getWhere('`p`.`categories` LIKE "%' . $name . '%" AND `p`.`trash` = 0 AND `p`.`publish_at` <= CURRENT_TIMESTAMP');
    }

    /**
     * @param int $id
     * @return Post
     * @throws Exception
     */
    public static function get(int $id): Post
    {
        return self::getObjectWhere('`p`.`post_id` = ?', [$id]);
    }

    /**
     * @return Post|null
     * @throws Exception
     */
    public function save(): ?Post
    {
        $pdo = self::getPDO();
        if ($this->getPostId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__post`
                SET `name` = ?, `enabled` = ?, `trash` = ?, `fk_language_id` = ?, `categories` = ?, `publish_at` = ?
                WHERE `post_id` = ?
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->isEnabled() ? 1 : 0,
                $this->isTrash() ? 1 : 0,
                $this->getLanguage()->getLanguageId(),
                join("\0", $this->getCategories()),
                $this->getPublishAt(),
                $this->getPostId()
            ]);
            return $res ? self::get($this->getPostId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__post` (`name`, `enabled`, `trash`, `fk_language_id`, `categories`, `publish_at`)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->isEnabled() ? 1 : 0,
                $this->isTrash() ? 1 : 0,
                $this->getLanguage()->getLanguageId(),
                join("\0", $this->getCategories()),
                $this->getPublishAt()
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
        if ($this->getPostId() > 0) {
            $stmt = $pdo->prepare("
                DELETE FROM `cms__post`
                WHERE `post_id` = ?
            ");
            return $stmt->execute([$this->getPostId()]);
        }
        return false;
    }

    /**
     * @return Post|null
     * @throws Exception
     */
    public function duplicate(): ?Post
    {
        $dup = self::get($this->postId);
        $dup->setPostId(0);
        $dup->setName($this->name . ' (Copy)');
        $dup->setEnabled(false);
        $dup = $dup->save();
        if (!is_null($dup)) {
            /** @var PostTranslation $trans */
            foreach (self::getPostTranslations() as $trans) {
                $trans->setPostTranslationId(0);
                $trans->setName($trans->getName() . '-copy');
                $trans->setPost($dup);
                $tres = $trans->save();
                if (is_null($tres)) {
                    $dup->delete();
                    return null;
                }
            }
            /** @var PostAttribute $attr */
            foreach (self::getPostAttributes() as $attr) {
                $attr->setPostAttributeId(0);
                $attr->setPost($dup);
                $ares = $attr->save();
                if (is_null($ares)) {
                    $dup->delete();
                    return null;
                }
            }
        } else {
            return null;
        }
        return $dup;
    }

    /**
     * @return PostTranslation
     * @throws Exception
     */
    public function getDefaultPostTranslation(): PostTranslation
    {
        return PostTranslation::getDefaultPostTranslation($this);
    }

    /**
     * @param Language $language
     * @return PostTranslation
     * @throws Exception
     */
    public function getPostTranslation(Language $language): PostTranslation
    {
        return PostTranslation::getPostTranslation($this, $language);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPostTranslations(): array
    {
        return PostTranslation::getPostTranslations($this);
    }

    /**
     * @return array
     * @throws Exception
     */
    public function getPostAttributes(): array
    {
        return PostAttribute::getPostAttributes($this);
    }

    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * @param int $postId
     */
    public function setPostId(int $postId): void
    {
        $this->postId = $postId;
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
    public function getPublishAt(): string
    {
        return $this->publishAt;
    }

    /**
     * @param string $publishAt
     */
    public function setPublishAt(string $publishAt): void
    {
        $this->publishAt = $publishAt;
    }

    /**
     * @return string[]
     */
    public function getCategories(): array
    {
        return $this->categories;
    }

    /**
     * @param string[] $categories
     */
    public function setCategories(array $categories): void
    {
        $this->categories = $categories;
    }

    /**
     * @return PostAttribute[]
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param PostAttribute[] $attributes
     */
    public function setAttributes(array $attributes): void
    {
        $this->attributes = $attributes;
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
