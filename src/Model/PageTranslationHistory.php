<?php

namespace GislerCMS\Model;

/**
 * Class PageTranslationHistory
 * @package GislerCMS\Model
 */
class PageTranslationHistory extends DbModel
{
    /**
     * @var int
     */
    private $pageTranslationHistoryId;

    /**
     * @var PageTranslation
     */
    private $pageTranslation;

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
     * @var User
     */
    private $user;

    /**
     * PageTranslationHistory constructor.
     * @param int $pageTranslationHistoryId
     * @param PageTranslation $pageTranslation
     * @param string $name
     * @param string $title
     * @param string $content
     * @param string $metaKeywords
     * @param string $metaDescription
     * @param string $metaAuthor
     * @param string $metaCopyright
     * @param string $metaImage
     * @param bool $enabled
     * @param User $user
     */
    public function __construct(
        int $pageTranslationHistoryId = 0,
        PageTranslation $pageTranslation = null,
        string $name = '',
        string $title = '',
        string $content = '',
        string $metaKeywords = '',
        string $metaDescription = '',
        string $metaAuthor = '',
        string $metaCopyright = '',
        string $metaImage = '',
        bool $enabled = false,
        User $user = null
    ) {
        $this->pageTranslationHistoryId = $pageTranslationHistoryId;
        $this->pageTranslation = $pageTranslation;
        $this->name = $name;
        $this->title = $title;
        $this->content = $content;
        $this->metaKeywords = $metaKeywords;
        $this->metaDescription = $metaDescription;
        $this->metaAuthor = $metaAuthor;
        $this->metaCopyright = $metaCopyright;
        $this->metaImage = $metaImage;
        $this->enabled = $enabled;
        $this->user = $user;
    }

    /**
     * @return int
     */
    public function getPageTranslationHistoryId(): int
    {
        return $this->pageTranslationHistoryId;
    }

    /**
     * @param int $pageTranslationHistoryId
     */
    public function setPageTranslationHistoryId(int $pageTranslationHistoryId)
    {
        $this->pageTranslationHistoryId = $pageTranslationHistoryId;
    }

    /**
     * @return PageTranslation
     */
    public function getPageTranslation(): PageTranslation
    {
        return $this->pageTranslation;
    }

    /**
     * @param PageTranslation $pageTranslation
     */
    public function setPageTranslation(PageTranslation $pageTranslation)
    {
        $this->pageTranslation = $pageTranslation;
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
    public function setName(string $name)
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
    public function setTitle(string $title)
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
    public function setContent(string $content)
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
    public function setMetaKeywords(string $metaKeywords)
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
    public function setMetaDescription(string $metaDescription)
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
    public function setMetaAuthor(string $metaAuthor)
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
    public function setMetaCopyright(string $metaCopyright)
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
    public function setMetaImage(string $metaImage)
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
    public function setEnabled(bool $enabled)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }
}
