<?php

namespace GislerCMS;

use GislerCMS\Controller\IndexController;
use GislerCMS\Controller\LoginController;
use GislerCMS\Controller\LogoutController;
use GislerCMS\Controller\SetupController;
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
        // Login
        $this->app->map(LoginController::METHODS, LoginController::PATTERN, LoginController::class)
            ->add(NoLoginMiddleware::class)
            ->setName(LoginController::NAME);

        // Logout
        $this->app->map(LogoutController::METHODS, LogoutController::PATTERN, LogoutController::class)
            ->setName(LogoutController::NAME);

        // Setup
        $this->app->map(SetupController::METHODS, SetupController::PATTERN, SetupController::class)
            ->setName(SetupController::NAME);

        // Dashboard
        $this->app->map(IndexController::METHODS, IndexController::PATTERN, IndexController::class)
            ->add(LoginMiddleware::class)
            ->setName(IndexController::NAME);
    }
}
