<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use PDOStatement;

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
    private $userAgent;

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
     * @param string $userAgent
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
        string $userAgent = '',
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
        $this->userAgent = $userAgent;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Session[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("
            SELECT 
                   
                `s`.`session_id`,
                `s`.`uuid`,
                `s`.`ip`,
                `s`.`platform`,
                `s`.`browser`,
                `s`.`user_agent`,
                `s`.`created_at`,
                `s`.`updated_at`,
                `c`.`client_id` AS 'c_client_id',
                `c`.`uuid` AS 'c_uuid',
                `c`.`created_at` AS 'c_created_at',
                `c`.`updated_at` AS 'c_updated_at'
                   
            FROM `cms__session` `s`
            
            INNER JOIN `cms__client` `c`
            ON `s`.`fk_client_id` = `c`.`client_id`
            
            " . (!empty($where) ? 'WHERE ' . $where : '') . "
        ");
        if ($stmt instanceof PDOStatement) {
            $stmt->execute($args);
            $sessions = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (sizeof($sessions) > 0) {
                foreach ($sessions as $session) {
                    $arr[] = new Session(
                        $session->session_id,
                        new Client(
                            $session->c_client_id,
                            $session->c_uuid,
                            $session->c_created_at,
                            $session->c_updated_at
                        ),
                        $session->uuid,
                        $session->ip,
                        $session->platform,
                        $session->browser,
                        $session->user_agent ?: '',
                        $session->created_at,
                        $session->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Session
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): Session
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new Session();
    }

    /**
     * @param int Session
     * @return Session
     * @throws Exception
     */
    public static function get(int $id): Session
    {
        return self::getObjectWhere('`s`.`session_id` = ?', [$id]);
    }

    /**
     * @param string $uuid
     * @return Session
     * @throws Exception
     */
    public static function getSession(string $uuid): Session
    {
        return self::getObjectWhere('`s`.`uuid` = ?', [$uuid]);
    }

    /**
     * @return Session[]
     * @throws Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @return Session|null
     * @throws Exception
     */
    public function save(): ?Session
    {
        $pdo = self::getPDO();
        if ($this->getSessionId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__session`
                SET `fk_client_id` = ?, `uuid` = ?, `ip` = ?, `platform` = ?, `browser` = ?, `user_agent` = ?, `updated_at` = CURRENT_TIMESTAMP()
                WHERE `session_id` = ?
            ");
            $res = $stmt->execute([
                $this->getClient()->getClientId(),
                $this->getUuid(),
                $this->getIp(),
                $this->getPlatform(),
                $this->getBrowser(),
                $this->getUserAgent(),
                $this->getSessionId()
            ]);
            return $res ? self::get($this->getSessionId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__session` (`fk_client_id`, `uuid`, `ip`, `platform`, `browser`, `user_agent`)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $res = $stmt->execute([
                $this->getClient()->getClientId(),
                $this->getUuid(),
                $this->getIp(),
                $this->getPlatform(),
                $this->getBrowser(),
                $this->getUserAgent()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
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
    public function getUserAgent(): string
    {
        return $this->userAgent;
    }

    /**
     * @param string $userAgent
     */
    public function setUserAgent(string $userAgent): void
    {
        $this->userAgent = $userAgent;
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
