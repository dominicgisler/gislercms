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
     * @return int
     */
    public function getPageId(): int
    {
        return $this->pageId;
    }

    /**
     * @param int $pageId
     */
    public function setPageId(int $pageId)
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
    public function setName(string $name)
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
    public function setEnabled(bool $enabled)
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
    public function setTrash(bool $trash)
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
    public function setLanguage(Language $language)
    {
        $this->language = $language;
    }
}
