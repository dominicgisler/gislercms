<?php

namespace GislerCMS\Controller;

use Exception;
use GislerCMS\Controller\Admin\Misc\System\BackupController;
use GislerCMS\Model\Config;
use GislerCMS\Model\DbModel;
use Psr\Container\ContainerInterface;
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
    private $container;

    /**
     * @param Container|ContainerInterface $container
     */
    public function __construct($container)
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
     */
    protected function get(string $var)
    {
        return $this->container->get($var);
    }

    /**
     * @param Request $request
     * @param Response $response
     * @return Response
     * @throws Exception
     */
    public function __invoke(Request $request, Response $response): Response
    {
        $intervalStats = Config::getConfig('global', 'interval_stats_refresh')->getValue();
        $intervalBackup = Config::getConfig('global', 'interval_backup')->getValue();

        if ($intervalStats > 0) {
            $this->handleStatistics($intervalStats);
        }

        if ($intervalBackup > 0) {
            $this->handleBackup($intervalBackup);
        }

        return $response;
    }

    /**
     * @param int $interval
     * @throws Exception
     */
    private function handleStatistics(int $interval)
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
     * @throws Exception
     */
    private function handleBackup(int $interval)
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
