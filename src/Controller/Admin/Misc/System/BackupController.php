<?php

namespace GislerCMS\Controller\Admin\Misc\System;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
use GislerCMS\Helper\SessionHelper;
use Ifsnop\Mysqldump\Mysqldump;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Stream;
use SplFileInfo;
use ZipArchive;

/**
 * Class BackupController
 * @package GislerCMS\Controller\Admin\Misc\System
 */
class BackupController extends AbstractController
{
    const NAME = 'admin-misc-system-backup';
    const PATTERN = '{admin_route}/misc/system/backup[/download/{backup}]';
    const METHODS = ['GET', 'POST'];

    const TIME_LIMIT = 300;
    const BACKUP_FOLDER = 'backups';
    const BACKUP_FILE = '%s/backup-%s-%s.zip';

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        set_time_limit(self::TIME_LIMIT);

        $rootPath = $this->get('settings')['root_path'];
        $backupPath = realpath($rootPath . '/' . self::BACKUP_FOLDER);

        $dlfile = $request->getAttribute('route')->getArgument('backup');
        if (!empty($dlfile)) {
            $file = sprintf('%s/%s', $backupPath, $dlfile);
            if (file_exists($file)) {
                $ctype = mime_content_type($file);
                $str = fopen($file, 'r');
                return $response
                    ->withHeader('Content-Type', $ctype)
                    ->withBody(new Stream($str));
            }
        }

        $msg = false;

        $cnt = SessionHelper::getContainer();
        if ($cnt->offsetExists('backup_deleted')) {
            $cnt->offsetUnset('backup_deleted');
            $msg = 'delete_success';
        } else if ($cnt->offsetExists('backup_created')) {
            $cnt->offsetUnset('backup_created');
            $msg = 'backup_success';
        }

        if ($request->isPost()) {
            if (!is_null($request->getParsedBodyParam('backup'))) {
                $dbCfg = $this->get('settings')['database'];
                $cmsVersion = $this->get('settings')['version'];
                self::doBackup($rootPath, $dbCfg, $cmsVersion);
                $cnt->offsetSet('backup_created', true);
            } else if (!is_null($request->getParsedBodyParam('delete'))) {
                $filename = $request->getParsedBodyParam('filename');
                $path = $backupPath . '/' . $filename;
                if (file_exists($path)) {
                    unlink($path);
                    $cnt->offsetSet('backup_deleted', true);
                }
            }

            return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/misc/system/backup');
        }

        $backups = self::getBackups($rootPath, true);

        return $this->render($request, $response, 'admin/misc/system/backup.twig', [
            'backups' => $backups,
            'message' => $msg
        ]);
    }

    /**
     * @param $bytes
     * @return string
     */
    private static function humanFilesize($bytes): string
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.2f", $bytes / pow(1024, $factor)) . @$sz[$factor ?: 0];
    }

    /**
     * @param string $rootPath
     * @param bool $withSize
     * @return array
     */
    public static function getBackups(string $rootPath, bool $withSize): array
    {
        $backupPath = realpath($rootPath . '/' . self::BACKUP_FOLDER);

        $backups = [];
        foreach (glob($backupPath . '/backup-*.zip') as $path) {
            $filename = basename($path);
            preg_match('/^backup-(v[0-9]\.[0-9]\.[0-9])-([0-9]+)\.zip/', $filename, $matches);
            $version = '';
            $timestamp = '';
            $size = $withSize ? filesize($path) : 0;
            if (isset($matches[1]) && isset($matches[2])) {
                $version = $matches[1];
                $timestamp = $matches[2];
            }
            $backups[] = [
                'filename' => $filename,
                'version' => $version,
                'timestamp' => $timestamp,
                'size' => self::humanFilesize($size)
            ];
        }

        return $backups;
    }

    /**
     * @param string $rootPath
     * @param array $dbCfg
     * @param string $cmsVersion
     * @throws Exception
     */
    public static function doBackup(string $rootPath, array $dbCfg, string $cmsVersion)
    {
        $rootPath = realpath($rootPath);
        $backupPath = realpath($rootPath . '/' . self::BACKUP_FOLDER);

        $dumpFile = $rootPath . '/' . $dbCfg['data'] . '.sql';
        $dump = new Mysqldump(
            sprintf('mysql:host=%s;dbname=%s;port=3306', $dbCfg['host'], $dbCfg['data']),
            $dbCfg['user'],
            $dbCfg['pass']
        );
        $dump->start($dumpFile);

        $zip = new ZipArchive();
        $zip->open(sprintf(self::BACKUP_FILE, $backupPath, $cmsVersion, time()), ZipArchive::CREATE | ZipArchive::OVERWRITE);

        /** @var SplFileInfo[] $files */
        $files = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($rootPath),
            RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                if (substr($filePath, 0, strlen($backupPath)) !== $backupPath) {
                    $relativePath = substr($filePath, strlen($rootPath) + 1);
                    $zip->addFile($filePath, $relativePath);
                }
            }
        }

        $zip->close();
        unlink($dumpFile);
    }
}
