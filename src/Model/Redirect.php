<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use PDOStatement;

/**
 * Class Redirect
 * @package GislerCMS\Model
 */
class Redirect extends DbModel
{
    /**
     * @var int
     */
    private $redirectId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @var string
     */
    private $route;

    /**
     * @var string
     */
    private $location;

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
    protected static $table = 'cms__redirect';

    /**
     * Redirect constructor.
     * @param int $redirectId
     * @param string $name
     * @param bool $enabled
     * @param string $route
     * @param string $location
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int    $redirectId = 0,
        string $name = '',
        bool   $enabled = false,
        string $route = '',
        string $location = '',
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->redirectId = $redirectId;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->route = $route;
        $this->location = $location;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Redirect[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__redirect` " . (!empty($where) ? 'WHERE ' . $where : ''));
        if ($stmt instanceof PDOStatement) {
            $stmt->execute($args);
            $redirects = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (sizeof($redirects) > 0) {
                foreach ($redirects as $redirect) {
                    $arr[] = new Redirect(
                        $redirect->redirect_id,
                        $redirect->name,
                        $redirect->enabled,
                        $redirect->route,
                        $redirect->location,
                        $redirect->created_at,
                        $redirect->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Redirect
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): Redirect
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new Redirect();
    }

    /**
     * @return Redirect[]
     * @throws Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @param int $id
     * @return Redirect
     * @throws Exception
     */
    public static function get(int $id): Redirect
    {
        return self::getObjectWhere('`redirect_id` = ?', [$id]);
    }

    /**
     * @param string $name
     * @return Redirect
     * @throws Exception
     */
    public static function getByName(string $name): Redirect
    {
        return self::getObjectWhere('`name` = ?', [$name]);
    }

    /**
     * @param string $route
     * @return Redirect
     * @throws Exception
     */
    public static function getByRoute(string $route): Redirect
    {
        return self::getObjectWhere('`route` = ? AND `enabled` = 1', [$route]);
    }

    /**
     * @return Redirect|null
     * @throws Exception
     */
    public function save(): ?Redirect
    {
        $pdo = self::getPDO();
        if ($this->getRedirectId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__redirect`
                SET `name` = ?, `enabled` = ?, `route` = ?, `location` = ?
                WHERE `redirect_id` = ?
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->isEnabled() ? 1 : 0,
                $this->getRoute(),
                $this->getLocation(),
                $this->getRedirectId()
            ]);
            return $res ? self::get($this->getRedirectId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__redirect` (`name`, `enabled`, `route`, `location`)
                VALUES (?, ?, ?, ?)
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->isEnabled() ? 1 : 0,
                $this->getRoute(),
                $this->getLocation()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function delete(): bool
    {
        $pdo = self::getPDO();
        if ($this->getRedirectId() > 0) {
            $stmt = $pdo->prepare("
                DELETE FROM `cms__redirect`
                WHERE `redirect_id` = ?
            ");
            return $stmt->execute([$this->getRedirectId()]);
        }
        return false;
    }

    /**
     * @return int
     */
    public function getRedirectId(): int
    {
        return $this->redirectId;
    }

    /**
     * @param int $redirectId
     */
    public function setRedirectId(int $redirectId): void
    {
        $this->redirectId = $redirectId;
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
    public function getRoute(): string
    {
        return $this->route;
    }

    /**
     * @param string $route
     */
    public function setRoute(string $route): void
    {
        $this->route = $route;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
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
