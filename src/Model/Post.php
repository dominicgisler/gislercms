<?php

namespace GislerCMS\Model;

/**
 * Class Post
 * @package GislerCMS\Model
 */
class Post extends DbModel
{
    /**
     * @var int
     */
    private $postId;

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
    private $publishAt;

    /**
     * @var string[]
     */
    private $categories;

    /**
     * @var array
     */
    private $attributes;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * Post constructor.
     * @param int $postId
     * @param string $name
     * @param bool $enabled
     * @param bool $trash
     * @param Language $language
     * @param string $publishAt
     * @param string[] $categories
     * @param array $attributes
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int $postId = 0,
        string $name = '',
        bool $enabled = true,
        bool $trash = false,
        Language $language = null,
        string $publishAt = '',
        array $categories = [],
        array $attributes = [],
        string $createdAt = '',
        string $updatedAt = ''
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
     * @return Post[]
     * @throws \Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @return Post[]
     * @throws \Exception
     */
    public static function getAvailable(): array
    {
        return self::getWhere('`p`.`trash` = 0');
    }

    /**
     * @return Post[]
     * @throws \Exception
     */
    public static function getTrash(): array
    {
        return self::getWhere('`p`.`trash` = 1');
    }

    /**
     * @param string $where
     * @return Post[]
     * @throws \Exception
     */
    private static function getWhere($where = ''): array
    {
        $arr = [];
        $stmt = self::getPDO()->query("
            SELECT
                `p`.`post_id`,
                `p`.`name`,
                `p`.`enabled`,
                `p`.`trash`,
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
            
            ORDER BY `enabled` DESC, `name` ASC
        ");
        if ($stmt instanceof \PDOStatement) {
            $posts = $stmt->fetchAll(\PDO::FETCH_OBJ);
            if (sizeof($posts) > 0) {
                foreach ($posts as $post) {
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
                        [], // TODO: add categories
                        [], // TODO: add attributes
                        $post->created_at,
                        $post->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param int $id
     * @return Post
     * @throws \Exception
     */
    public static function get(int $id): Post
    {
        $stmt = self::getPDO()->prepare("
            SELECT
                `p`.`post_id`,
                `p`.`name`,
                `p`.`enabled`,
                `p`.`trash`,
                `p`.`publish_at`,
                `p`.`created_at`,
                `p`.`updated_at`,
                `l`.`language_id`,
                `l`.`locale`,
                `l`.`description`,
                `l`.`enabled` AS 'l_enabled',
                `l`.`created_at` AS 'l_created_at',
                `l`.`updated_at` AS 'l_updated_at',
                GROUP_CONCAT(`c`.`name` SEPARATOR 0x0) AS `categories`
            
            FROM `cms__post` `p`
              
            INNER JOIN `cms__language` `l`
            ON `p`.fk_language_id = `l`.language_id
            
            LEFT JOIN `cms__post_category` `c`
            ON `p`.`post_id` = `c`.`fk_post_id`
            
            WHERE `p`.`post_id` = ?
        ");
        $stmt->execute([$id]);
        $post = $stmt->fetchObject();
        if ($post && $post->post_id > 0) {
            $cats = explode("\0", $post->categories);
            if (sizeof($cats) == 1 && $cats[0] == '') {
                $cats = [];
            }
            return new Post(
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
                [], // TODO: add attributes
                $post->created_at,
                $post->updated_at
            );
        }
        return new Post();
    }

    /**
     * @return Post|null
     * @throws \Exception
     */
    public function save(): ?Post
    {
        $pdo = self::getPDO();
        if ($this->getPostId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__post`
                SET `name` = ?, `enabled` = ?, `trash` = ?, `fk_language_id` = ?, `publish_at` = ?
                WHERE `post_id` = ?
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->isEnabled(),
                $this->isTrash(),
                $this->getLanguage()->getLanguageId(),
                $this->getPublishAt(),
                $this->getPostId()
            ]);
            return $res ? self::get($this->getPostId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__post` (`name`, `enabled`, `trash`, `fk_language_id`, `publish_at`)
                VALUES (?, ?, ?, ?, ?)
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->isEnabled(),
                $this->isTrash(),
                $this->getLanguage()->getLanguageId(),
                $this->getPublishAt()
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
     * @return PostTranslation
     * @throws \Exception
     */
    public function getDefaultPostTranslation()
    {
        return PostTranslation::getDefaultPostTranslation($this);
    }

    /**
     * @param Language $language
     * @return PostTranslation
     * @throws \Exception
     */
    public function getPostTranslation(Language $language)
    {
        return PostTranslation::getPostTranslation($this, $language);
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
     * @return array
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
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
