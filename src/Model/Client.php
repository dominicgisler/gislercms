<?php

namespace GislerCMS\Model;

/**
 * Class Client
 * @package GislerCMS\Model
 */
class Client extends DbModel
{
    /**
     * @var int
     */
    private $clientId;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * Client constructor.
     * @param int $clientId
     * @param string $uuid
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int $clientId = 0,
        string $uuid = '',
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->clientId = $clientId;
        $this->uuid = $uuid;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getClientId(): int
    {
        return $this->clientId;
    }

    /**
     * @param int $clientId
     */
    public function setClientId(int $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @return string
     */
    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @param string $uuid
     */
    public function setUuid(string $uuid): void
    {
        $this->uuid = $uuid;
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
