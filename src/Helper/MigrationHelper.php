<?php

namespace GislerCMS\Helper;

use GislerCMS\Model\DbModel;
use GislerCMS\Model\Migration;

/**
 * Class MigrationHelper
 * @package GislerCMS\Helper
 */
class MigrationHelper
{
    const MIGRATIONS = __DIR__ . '/../../mysql/*.sql';

    /**
     * @param \PDO $pdo
     * @return array
     * @throws \Exception
     */
    public static function executeMigrations(\PDO $pdo): array
    {
        $response = [];
        $error = false;
        DbModel::init($pdo);
        foreach (self::getMigrations() as $migration) {
            if ($error === false) {
                $m = Migration::getMigration($migration['name']);
                if ($m->getMigrationId() === 0) {
                    if ($migration['sql']) {
                        $res = $pdo->exec($migration['sql']);
                        if ($res === false) {
                            $err = $pdo->errorInfo();
                            if ($err[0] !== '00000' && $err[0] !== '01000') {
                                $error = 'SQLSTATE[' . $err[0] . '] [' . $err[1] . '] ' . $err[2];
                            }
                        }
                    } else {
                        $error = 'Couldn\'t read file contents!';
                    }
                    $response[$migration['name']] = [
                        'type' => $error !== false ? 'error' : 'success',
                        'message' => $error !== false ? $error : 'Success'
                    ];

                    if ($error === false) {
                        $mig = new Migration(0, $migration['name'], $migration['description']);
                        $mig->save();
                    }
                }
            }
        }
        return [
            'status' => $error !== false ? 'error' : 'success',
            'migrations' => $response
        ];
    }

    /**
     * @return array
     */
    public static function getMigrations(): array
    {
        $migrations = [];
        foreach (glob(self::MIGRATIONS) as $file) {
            $name = pathinfo($file)['filename'];
            $sql = file_get_contents($file);
            $comment = str_replace('-- ', '', strtok($sql, "\n"));

            $migrations[$name] = [
                'name' => $name,
                'description' => $comment,
                'path' => $file,
                'sql' => $sql
            ];
        }
        return $migrations;
    }
}
