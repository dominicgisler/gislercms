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
    private $value;

    /**
     * Config constructor.
     * @param int $configId
     * @param string $name
     * @param string $value
     */
    public function __construct(int $configId = 0, string $name = '', string $value = '')
    {
        $this->configId = $configId;
        $this->name = $name;
        $this->value = $value;
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
                $config->value
            );
        }
        return new Config();
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
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }
}
