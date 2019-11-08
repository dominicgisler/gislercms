<?php

namespace GislerCMS\Model;

/**
 * Class Session
 * @package GislerCMS\Model
 */
class Session extends DbModel
{
    /**
     * @var int
     */
    private $sessionId;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $uuid;

    /**
     * @var string
     */
    private $ip;

    /**
     * @var string
     */
    private $platform;

    /**
     * @var string
     */
    private $browser;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * Session constructor.
     * @param int $sessionId
     * @param Client|null $client
     * @param string $uuid
     * @param string $ip
     * @param string $platform
     * @param string $browser
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int $sessionId = 0,
        Client $client = null,
        string $uuid = '',
        string $ip = '',
        string $platform = '',
        string $browser = '',
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->sessionId = $sessionId;
        $this->client = $client;
        $this->uuid = $uuid;
        $this->ip = $ip;
        $this->platform = $platform;
        $this->browser = $browser;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return int
     */
    public function getSessionId(): int
    {
        return $this->sessionId;
    }

    /**
     * @param int $sessionId
     */
    public function setSessionId(int $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
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
    public function getIp(): string
    {
        return $this->ip;
    }

    /**
     * @param string $ip
     */
    public function setIp(string $ip): void
    {
        $this->ip = $ip;
    }

    /**
     * @return string
     */
    public function getPlatform(): string
    {
        return $this->platform;
    }

    /**
     * @param string $platform
     */
    public function setPlatform(string $platform): void
    {
        $this->platform = $platform;
    }

    /**
     * @return string
     */
    public function getBrowser(): string
    {
        return $this->browser;
    }

    /**
     * @param string $browser
     */
    public function setBrowser(string $browser): void
    {
        $this->browser = $browser;
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
