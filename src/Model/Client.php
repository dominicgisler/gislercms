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
     * @param string $where
     * @param array $args
     * @return Client[]
     * @throws \Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__client` " . (!empty($where) ? 'WHERE ' . $where : ''));
        if ($stmt instanceof \PDOStatement) {
            $stmt->execute($args);
            $clients = $stmt->fetchAll(\PDO::FETCH_OBJ);
            if (sizeof($clients) > 0) {
                foreach ($clients as $client) {
                    $arr[] = new Client(
                        $client->client_id,
                        $client->uuid,
                        $client->created_at,
                        $client->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Client
     * @throws \Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): Client
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__client` " . (!empty($where) ? 'WHERE ' . $where : ''));
        $stmt->execute($args);
        $client = $stmt->fetchObject();
        if ($client) {
            return new Client(
                $client->client_id,
                $client->uuid,
                $client->created_at,
                $client->updated_at
            );
        }
        return new Client();
    }

    /**
     * @return Client|null
     * @throws \Exception
     */
    public function save(): ?Client
    {
        $pdo = self::getPDO();
        if ($this->getClientId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__client`
                SET `uuid` = ?, `updated_at` = CURRENT_TIMESTAMP()
                WHERE `client_id` = ?
            ");
            $res = $stmt->execute([
                $this->getUuid(),
                $this->getClientId()
            ]);
            return $res ? self::get($this->getClientId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__client` (`uuid`)
                VALUES (?)
            ");
            $res = $stmt->execute([
                $this->getUuid()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @param int $id
     * @return Client
     * @throws \Exception
     */
    public static function get(int $id): Client
    {
        return self::getObjectWhere('`client_id` = ?', [$id]);
    }

    /**
     * @param string $uuid
     * @return Client
     * @throws \Exception
     */
    public static function getClient(string $uuid): Client
    {
        return self::getObjectWhere('`uuid` = ?', [$uuid]);
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
