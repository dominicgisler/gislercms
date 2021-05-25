<?php

namespace GislerCMS\Controller\Admin\Misc\System;

use Exception;
use GislerCMS\Controller\Admin\AbstractController;
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
    const ROOT_PATH = __DIR__ . '/../../../../..';
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

        $rootPath = realpath(self::ROOT_PATH);
        $backupPath = realpath(self::ROOT_PATH . '/' . self::BACKUP_FOLDER);

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

        if ($request->isPost()) {
            $dbCfg = $this->get('settings')['database'];
            $dumpFile = $rootPath . '/' . $dbCfg['data'] . '.sql';
            $dump = new Mysqldump(
                sprintf('mysql:host=%s;dbname=%s;port=3306', $dbCfg['host'], $dbCfg['data']),
                $dbCfg['user'],
                $dbCfg['pass']
            );
            $dump->start($dumpFile);

            $cmsVersion = $this->get('settings')['version'];

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
            return $response->withRedirect($this->get('base_url') . $this->get('settings')['global']['admin_route'] . '/misc/system/backup');
        }

        $backups = [];
        foreach (glob($backupPath . '/backup-*.zip') as $path) {
            $filename = basename($path);
            preg_match('/^backup-(v[0-9]\.[0-9]\.[0-9])-([0-9]+)\.zip/', $filename, $matches);
            $version = '';
            $timestamp = '';
            $size = filesize($path);
            if (isset($matches[1]) && isset($matches[2])) {
                $version = $matches[1];
                $timestamp = $matches[2];
            }
            $backups[] = [
                'filename' => $filename,
                'version' => $version,
                'timestamp' => $timestamp,
                'size' => $this->humanFilesize($size)
            ];
        }

        return $this->render($request, $response, 'admin/misc/system/backup.twig', [
            'backups' => $backups
        ]);
    }

    /**
     * @param $bytes
     * @param int $decimals
     * @return string
     */
    function humanFilesize($bytes, $decimals = 2): string
    {
        $sz = 'BKMGTP';
        $factor = floor((strlen($bytes) - 1) / 3);
        return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor ?: 0];
    }
}
