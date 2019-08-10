<?php

namespace GislerCMS\Model;

/**
 * Class Config
 * @package GislerCMS\Model
 */
class Config extends DbModel
{
    /**
     * @var int
     */
    private $configId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $type;

    /**
     * @var mixed
     */
    private $value;

    /**
     * Config constructor.
     * @param int $configId
     * @param string $name
     * @param string $type
     * @param string $value
     */
    public function __construct(int $configId = 0, string $name = '', string $type = '', $value = null)
    {
        $this->configId = $configId;
        $this->name = $name;
        $this->type = $type;
        switch ($this->type) {
            case 'bool':
            case 'boolean':
                $this->value = $value ? true : false;
                break;
            case 'int':
            case 'integer':
                $this->value = intval($value);
                break;
            case 'json':
                $this->value = json_decode($value);
                break;
            default:
                $this->value = $value;
        }
    }

    /**
     * @return Config[]
     * @throws \Exception
     */
    public static function getAll(): array
    {
        $arr = [];
        $stmt = self::getPDO()->query("SELECT `config_id`, `name`, `type`, `value` FROM `cms__config`");
        if ($stmt instanceof \PDOStatement) {
            $configs = $stmt->fetchAll(\PDO::FETCH_OBJ);
            if (sizeof($configs) > 0) {
                foreach ($configs as $config) {
                    $arr[] = new Config(
                        $config->config_id,
                        $config->name,
                        $config->type,
                        $config->value
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $name
     * @return Config
     * @throws \Exception
     */
    public static function getConfig(string $name): Config
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__config` WHERE `name` = ?");
        $stmt->execute([$name]);
        $config = $stmt->fetchObject();
        if ($config) {
            return new Config(
                $config->config_id,
                $config->name,
                $config->type,
                $config->value
            );
        }
        return new Config();
    }

    /**
     * @param int $id
     * @return Config
     * @throws \Exception
     */
    public static function get(int $id): Config
    {
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__config` WHERE `config_id` = ?");
        $stmt->execute([$id]);
        $config = $stmt->fetchObject();
        if ($config) {
            return new Config(
                $config->config_id,
                $config->name,
                $config->type,
                $config->value
            );
        }
        return new Config();
    }

    /**
     * @return Config|null
     * @throws \Exception
     */
    public function save(): ?Config
    {
        $pdo = self::getPDO();
        if ($this->getConfigId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__config`
                SET `name` = ?, `type` = ?, `value` = ?
                WHERE `config_id` = ?
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->getType(),
                $this->getValueAsString(),
                $this->getConfigId()
            ]);
            return $res ? self::get($this->getConfigId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__config` (`name`, `type`, `value`)
                VALUES (?, ?, ?)
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->getType(),
                $this->getValueAsString()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
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
}
