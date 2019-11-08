<?php

namespace GislerCMS\Model;

/**
 * Class Visit
 * @package GislerCMS\Model
 */
class Visit extends DbModel
{
    /**
     * @var int
     */
    private $visitId;

    /**
     * @var PageTranslation
     */
    private $pageTranslation;

    /**
     * @var Session
     */
    private $session;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * Visit constructor.
     * @param int $visitId
     * @param PageTranslation|null $pageTranslation
     * @param Session|null $session
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int $visitId = 0,
        PageTranslation $pageTranslation = null,
        Session $session = null,
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->visitId = $visitId;
        $this->pageTranslation = $pageTranslation;
        $this->session = $session;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getVisitId(): int
    {
        return $this->visitId;
    }

    /**
     * @param int $visitId
     */
    public function setVisitId(int $visitId): void
    {
        $this->visitId = $visitId;
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
    public function setPageTranslation(PageTranslation $pageTranslation): void
    {
        $this->pageTranslation = $pageTranslation;
    }

    /**
     * @return Session
     */
    public function getSession(): Session
    {
        return $this->session;
    }

    /**
     * @param Session $session
     */
    public function setSession(Session $session): void
    {
        $this->session = $session;
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
