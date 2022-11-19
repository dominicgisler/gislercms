<?php

namespace GislerCMS\Controller;

use Exception;
use GislerCMS\Controller\Admin\Misc\System\BackupController;
use GislerCMS\Model\Config;
use GislerCMS\Model\DbModel;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Container;
use Slim\Http\Request;
use Slim\Http\Response;
use GislerCMS\Controller\Admin\IndexController as AdminIndexController;

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
}
