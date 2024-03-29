<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use PDOStatement;

/**
 * Class Config
 * @package GislerCMS\Model
 */
class Config extends DbModel
{
    /**
     * @var int
     */
    private int $configId;

    /**
     * @var string
     */
    private string $section;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $type;

    /**
     * @var mixed
     */
    private mixed $value;

    /**
     * @var string
     */
    private string $createdAt;

    /**
     * @var string
     */
    private string $updatedAt;

    /**
     * @var string
     */
    protected static string $table = 'cms__config';

    /**
     * Config constructor.
     * @param int $configId
     * @param string $section
     * @param string $name
     * @param string $type
     * @param mixed $value
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int    $configId = 0,
        string $section = '',
        string $name = '',
        string $type = '',
        mixed  $value = null,
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->configId = $configId;
        $this->section = $section;
        $this->name = $name;
        $this->type = $type;
        switch ($this->type) {
            case 'bool':
            case 'boolean':
                $this->value = boolval($value);
                break;
            case 'int':
            case 'integer':
                $this->value = intval($value);
                break;
            case 'json':
                $this->value = json_decode($value, true);
                break;
            default:
                $this->value = $value;
        }
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Config[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__config` " . (!empty($where) ? 'WHERE ' . $where : ''));
        if ($stmt instanceof PDOStatement) {
            $stmt->execute($args);
            $configs = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (sizeof($configs) > 0) {
                foreach ($configs as $config) {
                    $arr[] = new Config(
                        $config->config_id,
                        $config->section,
                        $config->name,
                        $config->type,
                        $config->value,
                        $config->created_at,
                        $config->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Config
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): Config
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new Config();
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
     * @param string $section
     * @return Config[]
     * @throws Exception
     */
    public static function getBySection(string $section): array
    {
        return self::getWhere('`section` = ?', [$section]);
    }

    /**
     * @param string $section
     * @param string $name
     * @return Config
     * @throws Exception
     */
    public static function getConfig(string $section, string $name): Config
    {
        return self::getObjectWhere('`section` = ? AND `name` = ?', [$section, $name]);
    }

    /**
     * @param int $id
     * @return Config
     * @throws Exception
     */
    public static function get(int $id): Config
    {
        return self::getObjectWhere('`config_id` = ?', [$id]);
    }

    /**
     * @return Config|null
     * @throws Exception
     */
    public function save(): ?Config
    {
        $pdo = self::getPDO();
        if ($this->getConfigId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__config`
                SET `section` = ?, `name` = ?, `type` = ?, `value` = ?
                WHERE `config_id` = ?
            ");
            $res = $stmt->execute([
                $this->getSection(),
                $this->getName(),
                $this->getType(),
                $this->getValueAsString(),
                $this->getConfigId()
            ]);
            return $res ? self::get($this->getConfigId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__config` (`section`, `name`, `type`, `value`)
                VALUES (?, ?, ?, ?)
            ");
            $res = $stmt->execute([
                $this->getSection(),
                $this->getName(),
                $this->getType(),
                $this->getValueAsString()
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
        if ($this->getConfigId() > 0) {
            $stmt = $pdo->prepare("
                DELETE FROM `cms__config`
                WHERE `config_id` = ?
            ");
            return $stmt->execute([$this->getConfigId()]);
        }
        return false;
    }

    /**
     * @return int
     */
    public function getConfigId(): int
    {
        return $this->configId;
    }

    /**
     * @param int $configId
     */
    public function setConfigId(int $configId): void
    {
        $this->configId = $configId;
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
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    private function getValueAsString(): string
    {
        switch ($this->type) {
            case 'bool':
            case 'boolean':
                $value = $this->value ? '1' : '0';
                break;
            case 'json':
                $value = json_encode($this->value);
                break;
            default:
                $value = strval($this->value);
        }
        return $value;
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
     * @return string
     */
    public function getSection(): string
    {
        return $this->section;
    }

    /**
     * @param string $section
     */
    public function setSection(string $section): void
    {
        $this->section = $section;
    }
}
