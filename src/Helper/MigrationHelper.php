<?php

namespace GislerCMS\Helper;

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
     */
    public static function executeMigrations(\PDO $pdo): array
    {
        $response = [];
        $error = false;
        foreach (glob(self::MIGRATIONS) as $file) {
            if ($error === false) {
                $migration = pathinfo($file)['filename'];
                $sql = file_get_contents($file);

                if ($sql) {
                    $res = $pdo->exec($sql);
                    if ($res === false) {
                        $err = $pdo->errorInfo();
                        if ($err[0] !== '00000' && $err[0] !== '01000') {
                            $error = 'SQLSTATE[' . $err[0] . '] [' . $err[1] . '] ' . $err[2];
                        }
                    }
                } else {
                    $error = 'Couldn\'t read file contents!';
                }
                $response[$migration] = [
                    'type' => $error !== false ? 'error' : 'success',
                    'message' => $error !== false ? $error : 'Success'
                ];

                if ($error === false) {
                    $stmt = $pdo->prepare("INSERT INTO `cms__migration` (`migration`) VALUES (?)");
                    $res = $stmt->execute([$migration]);
                    if ($res === false) {
                        $err = $pdo->errorInfo();
                        if ($err[0] !== '00000' && $err[0] !== '01000') {
                            $error = 'SQLSTATE[' . $err[0] . '] [' . $err[1] . '] ' . $err[2];
                            $response[$migration . '__update_migration'] = [
                                'type' => 'error',
                                'message' => $error
                            ];
                        }
                    }
                }
            }
        }
        return [
            'status' => $error !== false ? 'error' : 'success',
            'migrations' => $response
        ];
    }
}
