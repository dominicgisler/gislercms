<?php

namespace GislerCMS;

use GislerCMS\Controller\IndexController;
use GislerCMS\Controller\LoginController;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;

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
        return include __DIR__ . '/../config/default.php';
    }

    /**
     * Kind of a service manager
     */
    protected function registerServices()
    {
        // services
        $container = $this->app->getContainer();

        $container['base_url'] = rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBaseUrl()), '/');

        $container['view'] = function ($container) {
            $twig = new Twig(__DIR__ . '/../templates', [
                //'cache' => __DIR__ . '/../cache'
                'cache' => false
            ]);

            // Instantiate and add Slim specific extension
            $twig->addExtension(new TwigExtension($container['router'], $container['base_url']));

            return $twig;
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
        $this->app->map(IndexController::METHODS, IndexController::PATTERN, IndexController::class)->setName(IndexController::NAME);
        $this->app->map(LoginController::METHODS, LoginController::PATTERN, LoginController::class)->setName(LoginController::NAME);
    }
}