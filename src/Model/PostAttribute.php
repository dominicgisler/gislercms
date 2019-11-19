<?php

namespace GislerCMS\Model;

/**
 * Class PostAttribute
 * @package GislerCMS\Model
 */
class PostAttribute extends DbModel
{
    /**
     * @var int
     */
    private $postAttributeId;

    /**
     * @var Post
     */
    private $post;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * PostTranslation constructor.
     * @param int $postAttributeId
     * @param Post $post
     * @param string $name
     * @param string $value
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int $postAttributeId = 0,
        Post $post = null,
        string $name = '',
        string $value = '',
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->postAttributeId = $postAttributeId;
        $this->post = $post;
        $this->name = $name;
        $this->value = $value;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @param bool $withPost
     * @return PostAttribute[]
     * @throws \Exception
     */
    public static function getWhere(string $where = '', array $args = [], bool $withPost = false): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT
                `post_attribute_id`,
                `fk_post_id`,
                `name`,
                `value`,
                `created_at`,
                `updated_at`
            
            FROM `cms__post_attribute`
            
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
        ");
        $stmt->execute($args);
        $postAttributes = $stmt->fetchAll(\PDO::FETCH_OBJ);
        if (sizeof($postAttributes) > 0) {
            foreach ($postAttributes as $postAttribute) {
                $arr[] = new PostAttribute(
                    $postAttribute->post_attribute_id,
                    $withPost ? Post::get($postAttribute->fk_post_id) : new Post(),
                    $postAttribute->name,
                    $postAttribute->value,
                    $postAttribute->created_at,
                    $postAttribute->updated_at
                );
            }
        }
        return $arr;
    }

    /**
     * @param int $id
     * @return PostAttribute
     * @throws \Exception
     */
    public static function get(int $id): PostAttribute
    {
        $arr = self::getWhere('`post_attribute_id` = ?', [$id]);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new PostAttribute();
    }

    /**
     * @param int $id
     * @return array
     * @throws \Exception
     */
    public static function getByPostId(int $id): array
    {
        return self::getWhere('`fk_post_id` = ?', [$id]);
    }

    /**
     * @param Post $post
     * @return PostAttribute[]
     * @throws \Exception
     */
    public static function getPostAttributes(Post $post): array
    {
        return self::getWhere('`fk_post_id` = ?', [$post->getPostId()]);
    }

    /**
     * @return PostAttribute|null
     * @throws \Exception
     */
    public function save(): ?PostAttribute
    {
        $pdo = self::getPDO();
        if ($this->getPostAttributeId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__post_attribute`
                SET `name` = ?, `value` = ?
                WHERE `post_attribute_id` = ?
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->getValue(),
                $this->getPostAttributeId()
            ]);
            return $res ? self::get($this->getPostAttributeId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__post_attribute` (
                    `fk_post_id`, `name`, `value`
                )
                VALUES (
                    ?, ?, ?
                )
            ");
            $res = $stmt->execute([
                $this->getPost()->getPostId(),
                $this->getName(),
                $this->getValue()
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
        if ($this->getPostAttributeId() > 0) {
            $stmt = $pdo->prepare("
                DELETE FROM `cms__post_attribute`
                WHERE `post_attribute_id` = ?
            ");
            return $stmt->execute([$this->getPostAttributeId()]);
        }
        return false;
    }

    /**
     * @return int
     */
    public function getPostAttributeId(): int
    {
        return $this->postAttributeId;
    }

    /**
     * @param int $postAttributeId
     */
    public function setPostAttributeId(int $postAttributeId): void
    {
        $this->postAttributeId = $postAttributeId;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post;
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
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
