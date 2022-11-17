<?php

namespace GislerCMS;

use Exception;
use GislerCMS\Middleware\NoCacheMiddleware;
use GislerCMS\TwigExtension\TwigFileExists;
use GislerCMS\TwigExtension\TwigGlob;
use GislerCMS\Middleware\LoginMiddleware;
use GislerCMS\Middleware\NoLoginMiddleware;
use GislerCMS\Model\DbModel;
use GislerCMS\TwigExtension\TwigGoogleReviews;
use GislerCMS\TwigExtension\TwigJsonDecode;
use GislerCMS\TwigExtension\TwigTrans;
use Locale;
use pavlakis\cli\CliRequest;
use PDO;
use PDOException;
use Slim\App;
use Slim\Views\Twig;
use Slim\Views\TwigExtension;
use Laminas\Config\Config;
use Throwable;

/**
 * Class Application
 * @package Gisler\Downloadlist
 */
class Application
{
    /**
     * @var App
     */
    protected App $app;

    private const LOCALE_MAP = [
        'de' => 'de_DE',
        'en' => 'en_US',
        'default' => 'de_DE'
    ];

    /**
     * Start that thing
     * @throws Throwable
     */
    public function run(): void
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
     * @throws Exception
     */
    protected function getSettings(): array
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
                $config->merge(new Config(include $filename));
            }
        }

        $cfg = $config->toArray();
        if (isset($cfg['settings']['database'])) {
            try {
                $db = $cfg['settings']['database'];
                $pdo = new PDO(
                    sprintf('mysql:host=%s;dbname=%s;port=3306;charset=utf8', $db['host'], $db['data']),
                    $db['user'],
                    $db['pass']
                );
                DbModel::init($pdo);
                foreach (Model\Config::getAll() as $elem) {
                    $cfg['settings'][$elem->getSection()][$elem->getName()] = $elem->getValue();
                }
            } catch (PDOException) {
            }
        }

        // override php settings
        foreach ($cfg['settings']['php'] as $key => $val) {
            if ($key != '' && $val != '') {
                ini_set($key, $val);
            }
        }

        return $cfg;
    }

    /**
     * Kind of a service manager
     */
    protected function registerServices(): void
    {
        $container = $this->app->getContainer();

        $container['base_url'] =
            rtrim(str_ireplace('index.php', '', $container['request']->getUri()->getBaseUrl()), '/');

        $container['view'] = function ($container) {
            $cfg = $container['settings']['renderer'];

            $paths = [];
            foreach ($cfg['template_paths'] as $path) {
                $path = sprintf($path, $container['settings']['theme']['name']);
                if (is_dir($path)) {
                    $paths[] = $path;
                }
            }

            $twig = new Twig($paths, [
                'cache' => $cfg['cache']
            ]);

            $twig->addExtension(new TwigExtension($container['router'], $container['base_url']));
            $twig->addExtension(new TwigGlob());
            $twig->addExtension(new TwigJsonDecode());
            $twig->addExtension(new TwigFileExists());
            $twig->addExtension(new TwigGoogleReviews());
            $twig->addExtension(new TwigTrans());

            return $twig;
        };

        $container['pdo'] = function ($container) {
            $cfg = $container['settings']['database'];

            return new PDO(
                sprintf('mysql:host=%s;dbname=%s;port=3306', $cfg['host'], $cfg['data']),
                $cfg['user'],
                $cfg['pass']
            );
        };
    }

    /**
     * Register middleware
     */
    protected function registerMiddleware(): void
    {
        $this->app->add(new CliRequest());
    }

    /**
     * Register routes
     */
    protected function registerRoutes(): void
    {
        $classes = $this->app->getContainer()['settings']['classes'];
        $adminRoute = $this->app->getContainer()['settings']['global']['admin_route'];

        foreach ($classes['require_login'] as $class) {
            $this->app->map($class::METHODS, str_replace('{admin_route}', $adminRoute, $class::PATTERN), $class)
                ->add(LoginMiddleware::class)
                ->add(NoCacheMiddleware::class)
                ->setName($class::NAME);
        }
        foreach ($classes['require_nologin'] as $class) {
            $this->app->map($class::METHODS, str_replace('{admin_route}', $adminRoute, $class::PATTERN), $class)
                ->add(NoLoginMiddleware::class)
                ->add(NoCacheMiddleware::class)
                ->setName($class::NAME);
        }
        foreach ($classes['default'] as $class) {
            $this->app->map($class::METHODS, str_replace('{admin_route}', $adminRoute, $class::PATTERN), $class)->setName($class::NAME);
        }
        foreach ($classes['default_nocache'] as $class) {
            $this->app->map($class::METHODS, str_replace('{admin_route}', $adminRoute, $class::PATTERN), $class)
                ->add(NoCacheMiddleware::class)
                ->setName($class::NAME);
        }
    }

    /**
     * @param string $locale
     */
    public static function setTransLocale(string $locale): void
    {
        if (in_array($locale, array_values(self::LOCALE_MAP))) {
            Locale::setDefault($locale);
        } else if (isset(self::LOCALE_MAP[$locale])) {
            Locale::setDefault(self::LOCALE_MAP[$locale]);
        } else {
            Locale::setDefault(self::LOCALE_MAP['default']);
        }
    }
}
