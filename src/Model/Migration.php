<?php

namespace GislerCMS\Model;

use Exception;
use PDO;
use PDOStatement;

/**
 * Class Migration
 * @package GislerCMS\Model
 */
class Migration extends DbModel
{
    /**
     * @var int
     */
    private int $migrationId;

    /**
     * @var string
     */
    private string $name;

    /**
     * @var string
     */
    private string $description;

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
    protected static string $table = 'cms__migration';

    /**
     * Config constructor.
     * @param int $migrationId
     * @param string $name
     * @param string $description
     * @param string $createdAt
     * @param string $updatedAt
     */
    public function __construct(
        int    $migrationId = 0,
        string $name = '',
        string $description = '',
        string $createdAt = '',
        string $updatedAt = ''
    )
    {
        $this->migrationId = $migrationId;
        $this->name = $name;
        $this->description = $description;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Migration[]
     * @throws Exception
     */
    public static function getWhere(string $where = '', array $args = []): array
    {
        $arr = [];
        $stmt = self::getPDO()->prepare("SELECT * FROM `cms__migration` " . (!empty($where) ? 'WHERE ' . $where : ''));
        if ($stmt instanceof PDOStatement) {
            $stmt->execute($args);
            $migs = $stmt->fetchAll(PDO::FETCH_OBJ);
            if (sizeof($migs) > 0) {
                foreach ($migs as $mig) {
                    $arr[] = new Migration(
                        $mig->migration_id,
                        $mig->name,
                        $mig->description,
                        $mig->created_at,
                        $mig->updated_at
                    );
                }
            }
        }
        return $arr;
    }

    /**
     * @param string $where
     * @param array $args
     * @return Migration
     * @throws Exception
     */
    public static function getObjectWhere(string $where = '', array $args = []): Migration
    {
        $arr = self::getWhere($where, $args);
        if (sizeof($arr) > 0) {
            return reset($arr);
        }
        return new Migration();
    }

    /**
     * @return Migration[]
     * @throws Exception
     */
    public static function getAll(): array
    {
        return self::getWhere();
    }

    /**
     * @param string $name
     * @return Migration
     * @throws Exception
     */
    public static function getMigration(string $name): Migration
    {
        return self::getObjectWhere('`name` = ?', [$name]);
    }

    /**
     * @param int $id
     * @return Migration
     * @throws Exception
     */
    public static function get(int $id): Migration
    {
        return self::getObjectWhere('`migration_id` = ?', [$id]);
    }

    /**
     * @return Migration|null
     * @throws Exception
     */
    public function save(): ?Migration
    {
        $pdo = self::getPDO();
        if ($this->getMigrationId() > 0) {
            $stmt = $pdo->prepare("
                UPDATE `cms__migration`
                SET `name` = ?, `description` = ?
                WHERE `migration_id` = ?
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->getDescription(),
                $this->getMigrationId()
            ]);
            return $res ? self::get($this->getMigrationId()) : null;
        } else {
            $stmt = $pdo->prepare("
                INSERT INTO `cms__migration` (`name`, `description`)
                VALUES (?, ?)
            ");
            $res = $stmt->execute([
                $this->getName(),
                $this->getDescription()
            ]);
            return $res ? self::get($pdo->lastInsertId()) : null;
        }
    }

    /**
     * @return bool
     * @throws Exception
     */
    public static function drop(): bool
    {
        $pdo = self::getPDO();
        $stmt = $pdo->prepare("DROP TABLE IF EXISTS `cms__migration`");
        return $stmt->execute();
    }

    /**
     * @return int
     */
    public function getMigrationId(): int
    {
        return $this->migrationId;
    }

    /**
     * @param int $migrationId
     */
    public function setMigrationId(int $migrationId): void
    {
        $this->migrationId = $migrationId;
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
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
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
