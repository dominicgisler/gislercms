<?php

namespace GislerCMS\Model;

/**
 * Class DbModel
 * @package GislerCMS\Model
 */
abstract class DbModel
{
    /**
     * @var \PDO
     */
    private static $pdo;

    /**
     * @param \PDO $pdo
     */
    public static function init(\PDO $pdo): void
    {
        self::$pdo = $pdo;
    }

    /**
     * @return \PDO
     * @throws \Exception
     */
    protected static function getPDO(): \PDO
    {
        if (self::$pdo instanceof \PDO) {
            return self::$pdo;
        }
        throw new \Exception('Please init $pdo first, use DbModel::init($pdo)');
    }
}
