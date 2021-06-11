<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use PDOStatement;

/**
 * Class Module
 * @package GislerCMS\Model
 */
class Module extends DbModel
{
    /**
     * @var int
     */
    private $moduleId;

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
     * @var string
     */
    private $controller;

    /**
     * @var string
     */
    private $config;

    /**
     * @var string
     */
    private $createdAt;

    /**
     * @var string
     */
    private $updatedAt;

    /**
     * Config constructor.
     * @param int $moduleId
     * @param string $name
     * @param bool $enabled
     * @param bool $trash
     * @param string $controller
     * @param string $config
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int $moduleId = 0,
        string $name = '',
        bool $enabled = false,
        bool $trash = false,
        string $controller = '',
        string $config = '',
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->moduleId = $moduleId;
        $this->name = $name;
        $this->enabled = $enabled;
        $this->trash = $trash;
        $this->controller = $controller;
        $this->config = $config;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Module[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__module` " . (!empty($where) ? 'WHERE ' . $where : ''));
        if ($stmt instanceof PDOStatement) {
            $stmt->execute($args);
            $modules = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (sizeof($modules) > 0) {
                foreach ($modules as $module) {
                    $arr[] = new Module(
                        $module->module_id,
                        $module->name,
                        $module->enabled,
                        $module->trash,
                        $module->controller,
                        $module->config,
                        $module->created_at,
                        $module->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Module
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): Module
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new Module();
    }

    /**
     * @return Config[]
     * @throws Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @param int $id
     * @return Module
     * @throws Exception
     */
    public static function get(int $id): Module
    {
        return self::getObjectWhere('`module_id` = ?', [$id]);
    }

    /**
     * @param string $name
     * @return Module
     * @throws Exception
     */
    public static function getByName(string $name): Module
    {
        return self::getObjectWhere('`name` = ?', [$name]);
    }

    /**
     * @return Module|null
     * @throws Exception
     */
    public function save(): ?Module
    {
        $pdo = self::getPDO();
        if ($this->getModuleId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__module`
                SET `name` = ?, `enabled` = ?, `trash` = ?, `controller` = ?, `config` = ?
                WHERE `module_id` = ?
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->isEnabled() ? 1 : 0,
                $this->isTrash() ? 1 : 0,
                $this->getController(),
                $this->getConfig(),
                $this->getModuleId()
            ]);
            return $res ? self::get($this->getModuleId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__module` (`name`, `enabled`, `trash`, `controller`, `config`)
                VALUES (?, ?, ?, ?, ?)
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->isEnabled() ? 1 : 0,
                $this->isTrash() ? 1 : 0,
                $this->getController(),
                $this->getConfig()
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
        if ($this->getModuleId() > 0) {
            $stmt = $pdo->prepare("
                DELETE FROM `cms__module`
                WHERE `module_id` = ?
            ");
            return $stmt->execute([$this->getModuleId()]);
        }
        return false;
    }

    /**
     * @return int
     */
    public function getModuleId(): int
    {
        return $this->moduleId;
    }

    /**
     * @param int $moduleId
     */
    public function setModuleId(int $moduleId): void
    {
        $this->moduleId = $moduleId;
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
     * @return bool
     */
    public function isTrash(): bool
    {
        return $this->trash;
    }

    /**
     * @param bool $trash
     */
    public function setTrash(bool $trash): void
    {
        $this->trash = $trash;
    }

    /**
     * @return string
     */
    public function getController(): string
    {
        return $this->controller;
    }

    /**
     * @param string $controller
     */
    public function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    /**
     * @return string
     */
    public function getConfig(): string
    {
        return $this->config;
    }

    /**
     * @param string $config
     */
    public function setConfig(string $config): void
    {
        $this->config = $config;
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
