<?php

namespace GislerCMS;

use GislerCMS\Controller\AdminIndexController;
use GislerCMS\Controller\AdminLoginController;
use GislerCMS\Controller\AdminLogoutController;
use GislerCMS\Controller\AdminPageAddController;
use GislerCMS\Controller\AdminPageEditController;
use GislerCMS\Controller\AdminPageTrashController;
use GislerCMS\Controller\AdminSetupController;
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
            __DIR__ . '/../config/' . APPLICATION_ENV . '.php',
            __DIR__ . '/../config/global.php',
            __DIR__ . '/../config/local.php'
        ];

        $config = new Config([]);
        foreach ($configPaths as $configPath) {
            foreach (glob($configPath) as $filename) {
                /** @noinspection PhpIncludeInspection */
                $config->merge(new Config(include $filename));
            }
        }

        return $config->toArray();
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

            $twig = new Twig($cfg['template_path'], [
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
        $classes = [
            'default' => [
                AdminLogoutController::class,
                AdminSetupController::class
            ],
            'require_login' => [
                AdminIndexController::class,
                AdminPageEditController::class,
                AdminPageAddController::class,
                AdminPageTrashController::class
            ],
            'require_nologin' => [
                AdminLoginController::class
            ]
        ];
        $adminRoute = $this->app->getContainer()['settings']['admin_route'];

        foreach ($classes['default'] as $class) {
            $this->app->map($class::METHODS, str_replace('{admin_route}', $adminRoute, $class::PATTERN), $class)->setName($class::NAME);
        }
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
    }
}
