<?php

namespace GislerCMS;

use GislerCMS\TwigExtension\TwigFileExists;
use GislerCMS\TwigExtension\TwigGlob;
use GislerCMS\Middleware\LoginMiddleware;
use GislerCMS\Middleware\NoLoginMiddleware;
use GislerCMS\Model\DbModel;
use GislerCMS\TwigExtension\TwigJsonDecode;
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
        $cfg = $this->getSettings();
        if (!empty($cfg['settings']['timezone'])) {
            date_default_timezone_set($cfg['settings']['timezone']);
        }

        $this->app = new App($cfg);
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
            __DIR__ . '/../config/navigation.php',
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
                    sprintf('mysql:host=%s;dbname=%s;port=3306;charset=utf8', $db['host'], $db['data']),
                    $db['user'],
                    $db['pass']
                );
                DbModel::init($pdo);
                foreach (Model\Config::getAll() as $elem) {
                    $cfg['settings'][$elem->getSection()][$elem->getName()] = $elem->getValue();
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
            $twig->addExtension(new TwigGlob());
            $twig->addExtension(new TwigJsonDecode());
            $twig->addExtension(new TwigFileExists());

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
