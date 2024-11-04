<?php

namespace GislerCMS\Controller;

use Exception;
use GislerCMS\Controller\Admin\Misc\System\BackupController;
use GislerCMS\Model\Config;
use GislerCMS\Model\DbModel;
use GislerCMS\Model\Module;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use GislerCMS\Controller\Admin\IndexController as AdminIndexController;
use ZipArchive;

/**
 * Class CronController
 * @package GislerCMS\Controller
 */
class CronController
{
    const NAME = 'cron';
    const PATTERN = '/cron';
    const METHODS = ['POST'];

    /**
     * @var Container|ContainerInterface
     */
    private Container|ContainerInterface $container;

    /**
     * @param ContainerInterface|Container $container
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface|Container $container)
    {
        if (PHP_SAPI != "cli") {
            exit;
        }
        $this->container = $container;
        DbModel::init($this->get('pdo'));
    }

    /**
     * @param string $var
     * @return mixed
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function get(string $var): mixed
    {
        return $this->container->get($var);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $intervalStats = Config::getConfig('global', 'interval_stats_refresh')->getValue();
        $intervalBackup = Config::getConfig('global', 'interval_backup')->getValue();

        if ($intervalBackup > 0) {
            $this->handleBackup($intervalBackup);
        }

        if ($intervalStats > 0) {
            $this->handleStatistics($intervalStats);
        }

        $this->handleGalleryArchive();

        return $response;
    }

    /**
     * @param int $interval
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function handleStatistics(int $interval): void
    {
        $cacheFile = $this->get('settings')['data_cache'] . 'dashboard.json';
        $doRefresh = true;
        if (file_exists($cacheFile)) {
            $cache = json_decode(file_get_contents($cacheFile), true);
            $lastCalc = strtotime($cache['calculation_date']);
            $limit = strtotime(sprintf('-%dhours', $interval));
            if ($lastCalc > $limit) {
                $doRefresh = false;
            }
        }
        if ($doRefresh) {
            AdminIndexController::calculateStats($cacheFile);
        }
    }

    /**
     * @param int $interval
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    private function handleBackup(int $interval): void
    {
        $rootPath = $this->get('settings')['root_path'];
        $backups = BackupController::getBackups($rootPath, false);
        $latest = 0;
        $limit = strtotime(sprintf('-%dhours', $interval));
        foreach ($backups as $backup) {
            if ($backup['timestamp'] > $latest) {
                $latest = $backup['timestamp'];
            }
        }
        if ($latest < $limit) {
            $dbCfg = $this->get('settings')['database'];
            $cmsVersion = $this->get('settings')['version'];
            BackupController::doBackup($rootPath, $dbCfg, $cmsVersion);
        }
    }

    /**
     * @return void
     * @throws Exception
     */
    private function handleGalleryArchive(): void
    {
        $mods = Module::getWhere('`controller` = ?', ['GalleryModuleController']);
        foreach ($mods as $mod) {
            $cfg = json_decode($mod->getConfig(), true);
            if (!isset($cfg['galleries']) || !is_array($cfg['galleries'])) {
                continue;
            }
            foreach ($cfg['galleries'] as $ident => $gal) {
                if (isset($gal['download']) && $gal['download']) {
                    $galPath = realpath(__DIR__ . '/../../public/uploads/' . $gal['path']);

                    $files = [];
                    foreach (glob($galPath . '/*.*') as $file) {
                        $relPath = substr($file, strlen($galPath) + 1);
                        $files[$relPath] = $file;
                    }

                    if (!isset($gal['files']) || !is_array($gal['files'])) {
                        $gal['files'] = [];
                    }

                    $hasChange = false;
                    if (sizeof($gal['files']) != sizeof($files)) {
                        $hasChange = true;
                    } else {
                        foreach ($gal['files'] as $file) {
                            if (!array_key_exists($file, $files)) {
                                $hasChange = true;
                            }
                        }
                        foreach ($files as $relPath => $file) {
                            if (!in_array($relPath, $gal['files'])) {
                                $hasChange = true;
                            }
                        }
                    }

                    $zipPath = $galPath . '/../' . $ident . '.zip';
                    if ($hasChange || !file_exists($zipPath)) {
                        $tmpfile = tempnam(sys_get_temp_dir(), 'gallery-download');
                        if (sizeof($files) > 0) {
                            $zip = new ZipArchive();
                            $zip->open($tmpfile, ZipArchive::CREATE | ZipArchive::OVERWRITE);
                            foreach ($files as $relPath => $file) {
                                $zip->addFile($file, $relPath);
                            }
                            $zip->close();
                        }
                        if (file_exists($zipPath)) {
                            unlink($zipPath);
                        }
                        if (sizeof($files) > 0 && file_exists($tmpfile)) {
                            rename($tmpfile, $zipPath);
                        }
                    }

                    $gal['files'] = array_keys($files);
                    $cfg['galleries'][$ident] = $gal;
                }
            }
            $mod->setConfig(json_encode($cfg, JSON_PRETTY_PRINT));
            $mod->save();
        }
    }
}
