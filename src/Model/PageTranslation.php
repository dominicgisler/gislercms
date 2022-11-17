<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use Slim\Http\Request;
use Slim\Views\Twig;

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
     * @var string
     */
    protected static $table = 'cms__page_translation';

    /**
     * PageTranslation constructor.
     * @param int $pageTranslationId
     * @param Page|null $page
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
        int      $pageTranslationId = 0,
        Page     $page = null,
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
     * @param string $where
     * @param array $args
     * @return PageTranslation[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
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
                `t`.`updated_at`,
                `l`.`language_id` AS 'l_language_id',
                `l`.`locale` AS 'l_locale',
                `l`.`description` AS 'l_description',
                `l`.`enabled` AS 'l_enabled',
                `l`.`created_at` AS 'l_created_at',
                `l`.`updated_at` AS 'l_updated_at',
                `p`.`page_id` AS 'p_page_id',
                `p`.`name` AS 'p_name',
                `p`.`enabled` AS 'p_enabled',
                `p`.`trash` AS 'p_trash',
                `p`.`created_at` AS 'p_created_at',
                `p`.`updated_at` AS 'p_updated_at',
                `pl`.`language_id` AS 'pl_language_id',
                `pl`.`locale` AS 'pl_locale',
                `pl`.`description` AS 'pl_description',
                `pl`.`enabled` AS 'pl_enabled',
                `pl`.`created_at` AS 'pl_created_at',
                `pl`.`updated_at` AS 'pl_updated_at'
            
            FROM `cms__page_translation` `t`
              
            INNER JOIN `cms__language` `l`
            ON `t`.fk_language_id = `l`.language_id
            
            INNER JOIN `cms__page` `p`
            ON `t`.`fk_page_id` = `p`.`page_id`
              
            INNER JOIN `cms__language` `pl`
            ON `p`.fk_language_id = `pl`.language_id
            
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
        ");
        $stmt->execute($args);
        $pageTranslations = $stmt->fetchAll(PDO::FETCH_OBJ);
        if (sizeof($pageTranslations) > 0) {
            foreach ($pageTranslations as $pageTranslation) {
                $arr[$pageTranslation->l_locale] = new PageTranslation(
                    $pageTranslation->page_translation_id,
                    new Page(
                        $pageTranslation->p_page_id,
                        $pageTranslation->p_name,
                        $pageTranslation->p_enabled,
                        $pageTranslation->p_trash,
                        new Language(
                            $pageTranslation->pl_language_id,
                            $pageTranslation->pl_locale,
                            $pageTranslation->pl_description,
                            $pageTranslation->pl_enabled,
                            $pageTranslation->pl_created_at,
                            $pageTranslation->pl_updated_at
                        ),
                        $pageTranslation->p_created_at,
                        $pageTranslation->p_updated_at
                    ),
                    new Language(
                        $pageTranslation->l_language_id,
                        $pageTranslation->l_locale,
                        $pageTranslation->l_description,
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
     * @param string $where
     * @param array $args
     * @return PageTranslation
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): PageTranslation
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new PageTranslation();
    }

    /**
     * @param int $id
     * @return PageTranslation
     * @throws Exception
     */
    public static function get(int $id): PageTranslation
    {
        return self::getObjectWhere('`t`.`page_translation_id` = ?', [$id]);
    }

    /**
     * @param Page $page
     * @return PageTranslation[]
     * @throws Exception
     */
    public static function getPageTranslations(Page $page): array
    {
        return self::getWhere('`t`.`fk_page_id` = ?', [$page->getPageId()]);
    }

    /**
     * @param string $name
     * @return PageTranslation
     * @throws Exception
     */
    public static function getDefaultByName(string $name): PageTranslation
    {
        $res = new PageTranslation();
        $elems = self::getWhere('`t`.`name` = ?', [$name]);
        if (sizeof($elems) > 1) {
            foreach ($elems as $trans) {
                if ($trans->getLanguage()->getLanguageId() == $trans->getPage()->getLanguage()->getLanguageId()) {
                    $res = $trans;
                }
            }
        } elseif (sizeof($elems) == 1) {
            $res = reset($elems);
        }

        $parts = explode('/', $name);
        if (sizeof($parts) > 1 && $res->getPageTranslationId() == 0) {
            unset($parts[sizeof($parts) - 1]);
            return self::getDefaultByName(join('/', $parts));
        }

        return $res;
    }

    /**
     * @param string $name
     * @param Language $language
     * @return PageTranslation
     * @throws Exception
     */
    public static function getByName(string $name, Language $language): PageTranslation
    {
        $res = new PageTranslation();
        $elems = self::getWhere('`t`.`name` = ? AND `l`.`language_id` = ? AND `t`.`enabled` = 1', [$name, $language->getLanguageId()]);
        if (sizeof($elems) > 0) {
            $res = reset($elems);
        }

        $parts = explode('/', $name);
        if (sizeof($parts) > 1 && $res->getPageTranslationId() == 0) {
            unset($parts[sizeof($parts) - 1]);
            return self::getByName(join('/', $parts), $language);
        }

        return $res;
    }

    /**
     * @param Page $page
     * @return PageTranslation
     * @throws Exception
     */
    public static function getDefaultPageTranslation(Page $page): PageTranslation
    {
        return self::getObjectWhere('`t`.`fk_page_id` = ? AND `l`.`language_id` = ?', [$page->getPageId(), $page->getLanguage()->getLanguageId()]);
    }

    /**
     * @param Page $page
     * @param Language $language
     * @return PageTranslation
     * @throws Exception
     */
    public static function getPageTranslation(Page $page, Language $language): PageTranslation
    {
        $obj = self::getObjectWhere('`t`.`enabled` = 1 AND `t`.`fk_page_id` = ? AND `l`.`language_id` = ?', [$page->getPageId(), $language->getLanguageId()]);
        if ($obj->getPageTranslationId() > 0) {
            return $obj;
        }
        return self::getDefaultPageTranslation($page);
    }

    /**
     * @return PageTranslation|null
     * @throws Exception
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
                $this->isEnabled() ? 1 : 0,
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
                $this->isEnabled() ? 1 : 0
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

    /**
     * @throws Exception
     */
    public function replaceWidgets(): void
    {
        $this->setContent(self::replaceWidgetPlaceholders($this->getContent(), $this->getLanguage()));
    }

    /**
     * @param Request $request
     * @param Twig $view
     * @throws Exception
     */
    public function replaceModules(Request $request, Twig $view): void
    {
        $this->setContent(self::replaceModulePlaceholders($this->getContent(), $request->withAttribute('page', $this), $view));
    }

    /**
     * @param Request $request
     * @param Twig $view
     * @throws Exception
     */
    public function replacePosts(Request $request, Twig $view): void
    {
        $html = $this->getContent();
        $pattern = '#<pre class="posts">(.*?)</pre>#';
        while (preg_match($pattern, $html)) {
            $html = preg_replace_callback($pattern, function ($match) use ($request, $view) {
                $res = '';
                if (isset($match[1])) {
                    $name = $match[1];
                    $args = $request->getAttribute('arguments');
                    if (strlen($args) > 0) {
                        $trans = PostTranslation::getByName($args, $this->getLanguage());
                        $post = $trans->getPost();
                        if ($post->getPostId() > 0) {
                            $this->setName($this->getName() . '/' . $trans->getName());
                            if (!empty($trans->getMetaAuthor())) {
                                $this->setMetaAuthor(str_replace('{page}', $this->getMetaAuthor(), $trans->getMetaAuthor()));
                            }
                            if (!empty($trans->getMetaCopyright())) {
                                $this->setMetaCopyright(str_replace('{page}', $this->getMetaCopyright(), $trans->getMetaCopyright()));
                            }
                            if (!empty($trans->getMetaDescription())) {
                                $this->setMetaDescription($trans->getMetaDescription());
                            }
                            if (!empty($trans->getMetaImage())) {
                                $this->setMetaImage($trans->getMetaImage());
                            }
                            if (!empty($trans->getMetaKeywords())) {
                                $this->setMetaKeywords(str_replace('{page}', $this->getMetaKeywords(), $trans->getMetaKeywords()));
                            }
                            if (!empty($trans->getTitle())) {
                                $this->setTitle(str_replace('{page}', $this->getTitle(), $trans->getTitle()));
                            }
                            $this->setUpdatedAt($trans->getUpdatedAt());
                            return $view->fetch('posts/detail.twig', [
                                'post' => $post,
                                'trans' => $trans
                            ]);
                        }
                    }
                    $posts = Post::getByCategory($name);
                    $transList = [];
                    foreach ($posts as $post) {
                        $trans = PostTranslation::getPostTranslation($post, $this->getLanguage());
                        $transList[] = [
                            'post' => $post,
                            'trans' => $trans
                        ];
                    }
                    $res = $view->fetch('posts/list.twig', [
                        'pTrans' => $this,
                        'list' => $transList
                    ]);
                }
                return $res;
            }, $html);
        }
        $this->setContent($html);
    }
}
