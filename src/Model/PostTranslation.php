<?php

namespace GislerCMS\Model;

use Exception;
use PDO;

/**
 * Class PostTranslation
 * @package GislerCMS\Model
 */
class PostTranslation extends DbModel
{
    /**
     * @var int
     */
    private int $postTranslationId;

    /**
     * @var ?Post
     */
    private ?Post $post;

    /**
     * @var ?Language
     */
    private ?Language $language;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $title;

    /**
     * @var string
     */
    private string $content;

    /**
     * @var string
     */
    private string $metaKeywords;

    /**
     * @var string
     */
    private string $metaDescription;

    /**
     * @var string
     */
    private string $metaAuthor;

    /**
     * @var string
     */
    private string $metaCopyright;

    /**
     * @var string
     */
    private string $metaImage;

    /**
     * @var bool
     */
    private bool $enabled;

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
    protected static string $table = 'cms__post_translation';

    /**
     * PostTranslation constructor.
     * @param int $postTranslationId
     * @param Post|null $post
     * @param Language|null $language
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
        int      $postTranslationId = 0,
        Post     $post = null,
        Language $language = null,
        string   $name = '',
        string   $title = '',
        string   $content = '',
        string   $metaKeywords = '',
        string   $metaDescription = '',
        string   $metaAuthor = '',
        string   $metaCopyright = '',
        string   $metaImage = '',
        bool     $enabled = false,
        string   $createdAt = '',
        string   $updatedAt = ''
    )
    {
        $this->postTranslationId = $postTranslationId;
        $this->post = $post;
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
     * @param string $where
     * @param array $args
     * @return PostTranslation[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT
                `t`.`post_translation_id`,
                `t`.`fk_post_id`,
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
            
            FROM `cms__post_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            INNER JOIN `cms__post` `p`
            ON `t`.`fk_post_id` = `p`.`post_id`
            
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
        ");
        $stmt->execute($args);
        $postTranslations = $stmt->fetchAll(PDO::FETCH_OBJ);
        if (sizeof($postTranslations) > 0) {
            foreach ($postTranslations as $postTranslation) {
                $arr[$postTranslation->locale] = new PostTranslation(
                    $postTranslation->post_translation_id,
                    Post::get($postTranslation->fk_post_id),
                    new Language(
                        $postTranslation->language_id,
                        $postTranslation->locale,
                        $postTranslation->description,
                        $postTranslation->l_enabled,
                        $postTranslation->l_created_at,
                        $postTranslation->l_updated_at
                    ),
                    $postTranslation->name,
                    $postTranslation->title ?: '',
                    $postTranslation->content ?: '',
                    $postTranslation->meta_keywords ?: '',
                    $postTranslation->meta_description ?: '',
                    $postTranslation->meta_author ?: '',
                    $postTranslation->meta_copyright ?: '',
                    $postTranslation->meta_image ?: '',
                    $postTranslation->enabled,
                    $postTranslation->created_at,
                    $postTranslation->updated_at
                );
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return PostTranslation
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): PostTranslation
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new PostTranslation();
    }

    /**
     * @param int $id
     * @return PostTranslation
     * @throws Exception
     */
    public static function get(int $id): PostTranslation
    {
        return self::getObjectWhere('`t`.`post_translation_id` = ?', [$id]);
    }

    /**
     * @param Post $post
     * @return PostTranslation[]
     * @throws Exception
     */
    public static function getPostTranslations(Post $post): array
    {
        return self::getWhere('`t`.`fk_post_id` = ?', [$post->getPostId()]);
    }

    /**
     * @param string $name
     * @return PostTranslation
     * @throws Exception
     */
    public static function getDefaultByName(string $name): PostTranslation
    {
        $res = new PostTranslation();
        $elems = self::getWhere('`t`.`name` = ?', [$name]);
        if (sizeof($elems) > 1) {
            foreach ($elems as $trans) {
                if ($trans->getLanguage()->getLanguageId() == $trans->getPost()->getLanguage()->getLanguageId()) {
                    $res = $trans;
                }
            }
        } elseif (sizeof($elems) == 1) {
            $res = reset($elems);
        }

        $parts = explode('/', $name);
        if (sizeof($parts) > 1 && $res->getPostTranslationId() == 0) {
            unset($parts[sizeof($parts) - 1]);
            return self::getDefaultByName(join('/', $parts));
        }

        return $res;
    }

    /**
     * @param string $name
     * @param Language $language
     * @return PostTranslation
     * @throws Exception
     */
    public static function getByName(string $name, Language $language): PostTranslation
    {
        $res = new PostTranslation();
        $elems = self::getWhere('`t`.`name` = ? AND `l`.`language_id` = ? AND `t`.`enabled` = 1 AND `p`.`trash` = 0 AND `p`.`publish_at` <= CURRENT_TIMESTAMP', [$name, $language->getLanguageId()]);
        if (sizeof($elems) > 0) {
            $res = reset($elems);
        }

        $parts = explode('/', $name);
        if (sizeof($parts) > 1 && $res->getPostTranslationId() == 0) {
            unset($parts[sizeof($parts) - 1]);
            return self::getByName(join('/', $parts), $language);
        }

        return $res;
    }

    /**
     * @param Post $post
     * @return PostTranslation
     * @throws Exception
     */
    public static function getDefaultPostTranslation(Post $post): PostTranslation
    {
        return self::getObjectWhere('`t`.`fk_post_id` = ? AND `l`.`language_id` = ?', [$post->getPostId(), $post->getLanguage()->getLanguageId()]);
    }

    /**
     * @param Post $post
     * @param Language $language
     * @return PostTranslation
     * @throws Exception
     */
    public static function getPostTranslation(Post $post, Language $language): PostTranslation
    {
        $obj = self::getObjectWhere('`t`.`enabled` = 1 AND `t`.`fk_post_id` = ? AND `l`.`language_id` = ?', [$post->getPostId(), $language->getLanguageId()]);
        if ($obj->getPostTranslationId() > 0) {
            return $obj;
        }
        return self::getDefaultPostTranslation($post);
    }

    /**
     * @return PostTranslation|null
     * @throws Exception
     */
    public function save(): ?PostTranslation
    {
        $pdo = self::getPDO();
        if ($this->getPostTranslationId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__post_translation`
                SET `name` = ?, `title` = ?, `content` = ?, `meta_keywords` = ?, `meta_description` = ?,
                    `meta_author` = ?, `meta_copyright` = ?, `meta_image` = ?, `enabled` = ?
                WHERE `post_translation_id` = ?
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
                $this->isEnabled() ? 1 : 0,
                $this->getPostTranslationId()
            ]);
            return $res ? self::get($this->getPostTranslationId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__post_translation` (
                    `fk_post_id`, `fk_language_id`, `name`, `title`, `content`, `meta_keywords`,
                    `meta_description`, `meta_author`, `meta_copyright`, `meta_image`, `enabled`
                )
                VALUES (
                    ?, ?, ?, ?, ?, ?,
                    ?, ?, ?, ?, ?
                )
            ");
            $res = $stmt->execute([
                $this->getPost()->getPostId(),
                $this->getLanguage()->getLanguageId(),
                $this->getName(),
                $this->getTitle(),
                $this->getContent(),
                $this->getMetaKeywords(),
                $this->getMetaDescription(),
                $this->getMetaAuthor(),
                $this->getMetaCopyright(),
                $this->getMetaImage(),
                $this->isEnabled() ? 1 : 0
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @return int
     */
    public function getPostTranslationId(): int
    {
        return $this->postTranslationId;
    }

    /**
     * @param int $postTranslationId
     */
    public function setPostTranslationId(int $postTranslationId): void
    {
        $this->postTranslationId = $postTranslationId;
    }

    /**
     * @return Post
     */
    public function getPost(): Post
    {
        return $this->post ?: new Post();
    }

    /**
     * @param Post $post
     */
    public function setPost(Post $post): void
    {
        $this->post = $post;
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
