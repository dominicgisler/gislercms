<?php

namespace GislerCMS;

use GislerCMS\Controller\Admin\AdminIndexController;
use GislerCMS\Controller\Admin\AdminPreviewController;
use GislerCMS\Controller\Admin\AdminSetupController;
use GislerCMS\Controller\Admin\Auth\AdminLoginController;
use GislerCMS\Controller\Admin\Auth\AdminLogoutController;
use GislerCMS\Controller\Admin\Misc\AdminMiscChangePasswordController;
use GislerCMS\Controller\Admin\Misc\AdminMiscConfigController;
use GislerCMS\Controller\Admin\Misc\AdminMiscProfileController;
use GislerCMS\Controller\Admin\Misc\AdminMiscSysInfoController;
use GislerCMS\Controller\Admin\Page\AdminPageAddController;
use GislerCMS\Controller\Admin\Page\AdminPageDefaultsController;
use GislerCMS\Controller\Admin\Page\AdminPageEditController;
use GislerCMS\Controller\Admin\Page\AdminPageTrashController;
use GislerCMS\Controller\Admin\Widget\AdminWidgetAddController;
use GislerCMS\Controller\Admin\Widget\AdminWidgetEditController;
use GislerCMS\Controller\Admin\Widget\AdminWidgetTrashController;
use GislerCMS\Controller\IndexController;
use GislerCMS\Controller\PageController;
use GislerCMS\Controller\PageLangController;
use GislerCMS\Middleware\LoginMiddleware;
use GislerCMS\Middleware\NoLoginMiddleware;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Zend\Config\Config;

/**
 * Class Application
 * @package Gisler\Downloadlist
 */
class Application
{
    /**
     * @var App
     */
    protected $app;

    /**
     * Start that thing
     */
    public function run()
    {
        $this->app = new App($this->getSettings());
        $this->registerServices();
        $this->registerMiddleware();
        $this->registerRoutes();
        $this->app->run();
    }

    /**
     * @return array
     */
    protected function getSettings()
    {
        $configPaths = [
            __DIR__ . '/../config/default.php',
            __DIR__ . '/../config/custom.php',
            __DIR__ . '/../config/local.php'
        ];

        $config = new Config([]);
        foreach ($configPaths as $configPath) {
            foreach (glob($configPath) as $filename) {
                /** @noinspection PhpIncludeInspection */
                $config->merge(new Config(include $filename));
            }
        }

        $cfg = $config->toArray();
        if (isset($cfg['settings']['database'])) {
            try {
                $db = $cfg['settings']['database'];
                $pdo = new \PDO(
                    sprintf('mysql:host=%s;dbname=%s;port=3306', $db['host'], $db['data']),
                    $db['user'],
                    $db['pass']
                );
                $stmt = $pdo->query("SELECT * FROM `cms__config`");
                if ($stmt) {
                    $arr = $stmt->fetchAll(\PDO::FETCH_OBJ);
                    if (sizeof($arr) > 0) {
                        foreach ($arr as $elem) {
                            $cfg['settings'][$elem->section][$elem->name] = $elem->value;
                        }
                    }
                }
            } catch(\PDOException $e) {
            }
        }

        return $cfg;
    }

    /**
     * Kind of a service manager
     */
    protected function registerServices()
    {
        // services
        $container = $this->app->getContainer();

        $container['base_url'] =
            rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBaseUrl()), '/');

        $container['view'] = function ($container) {
            $cfg = $container['settings']['renderer'];

            $paths = [];
            foreach ($cfg['template_paths'] as $path) {
                if (is_dir($path)) {
                    $paths[] = $path;
                }
            }

            $twig = new Twig($paths, [
                'cache' => $cfg['cache']
            ]);

            // Instantiate and add Slim specific extension
            $twig->addExtension(new TwigExtension($container['router'], $container['base_url']));

            return $twig;
        };

        $container['pdo'] = function ($container) {
            $cfg = $container['settings']['database'];

            return new \PDO(
                sprintf('mysql:host=%s;dbname=%s;port=3306', $cfg['host'], $cfg['data']),
                $cfg['user'],
                $cfg['pass']
            );
        };
    }

    /**
     * Register middleware
     */
    protected function registerMiddleware()
    {
        // middleware
    }

    /**
     * Register routes
     */
    protected function registerRoutes()
    {
        $classes = $this->app->getContainer()['settings']['classes'];
        $adminRoute = $this->app->getContainer()['settings']['global']['admin_route'];

        foreach ($classes['require_login'] as $class) {
            $this->app->map($class::METHODS, str_replace('{admin_route}', $adminRoute, $class::PATTERN), $class)
                ->add(LoginMiddleware::class)
                ->setName($class::NAME);
        }
        foreach ($classes['require_nologin'] as $class) {
            $this->app->map($class::METHODS, str_replace('{admin_route}', $adminRoute, $class::PATTERN), $class)
                ->add(NoLoginMiddleware::class)
                ->setName($class::NAME);
        }
        foreach ($classes['default'] as $class) {
            $this->app->map($class::METHODS, str_replace('{admin_route}', $adminRoute, $class::PATTERN), $class)->setName($class::NAME);
        }
    }
}
