<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'timezone' => 'Europe/Zurich',

        // override php settings
        'php' => [
            'memory_limit' => '',
            'max_execution_time' => ''
        ],

        // Renderer settings
        'renderer' => [
            'template_paths' => [
                __DIR__ . '/../themes/%s/',
                __DIR__ . '/../custom_templates/',
                __DIR__ . '/../templates/',
            ],
            'cache' => false
        ],

        // Monolog settings
        'logger' => [
            'name' => 'gislercms',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        'database' => [
            'host' => 'localhost',
            'user' => 'root',
            'pass' => 'root',
            'data' => 'gislercms'
        ],

        'enable_setup' => true,

        'version' => 'v1.1.2',

        'global' => [
            'admin_route' => '/admin'
        ],

        'classes' => [
            'default' => [
                \GislerCMS\Controller\Admin\Auth\LogoutController::class,
                \GislerCMS\Controller\Admin\SetupController::class,
                \GislerCMS\Controller\SitemapController::class,
                \GislerCMS\Controller\AssetController::class,
                \GislerCMS\Controller\CronController::class,
                \GislerCMS\Controller\IndexController::class,
                \GislerCMS\Controller\PageLangController::class,
                \GislerCMS\Controller\PageController::class
            ],
            'require_login' => [
                \GislerCMS\Controller\Admin\IndexController::class,
                \GislerCMS\Controller\Admin\Stats\ClientsController::class,
                \GislerCMS\Controller\Admin\Stats\VisitsController::class,
                \GislerCMS\Controller\Admin\Stats\SessionsController::class,
                \GislerCMS\Controller\Admin\PreviewController::class,
                \GislerCMS\Controller\Admin\FilemanagerController::class,
                \GislerCMS\Controller\Admin\Page\DefaultsController::class,
                \GislerCMS\Controller\Admin\Page\ListController::class,
                \GislerCMS\Controller\Admin\Page\EditController::class,
                \GislerCMS\Controller\Admin\Post\ListController::class,
                \GislerCMS\Controller\Admin\Post\EditController::class,
                \GislerCMS\Controller\Admin\Widget\ListController::class,
                \GislerCMS\Controller\Admin\Widget\EditController::class,
                \GislerCMS\Controller\Admin\Misc\LicenseController::class,
                \GislerCMS\Controller\Admin\Misc\ProfileController::class,
                \GislerCMS\Controller\Admin\Misc\ChangePasswordController::class,
                \GislerCMS\Controller\Admin\Misc\TrashController::class,
                \GislerCMS\Controller\Admin\Misc\ThemeController::class,
                \GislerCMS\Controller\Admin\Misc\Language\ListController::class,
                \GislerCMS\Controller\Admin\Misc\Language\EditController::class,
                \GislerCMS\Controller\Admin\Misc\User\ListController::class,
                \GislerCMS\Controller\Admin\Misc\User\EditController::class,
                \GislerCMS\Controller\Admin\Misc\System\ConfigController::class,
                \GislerCMS\Controller\Admin\Misc\System\MailerController::class,
                \GislerCMS\Controller\Admin\Misc\System\SysInfoController::class,
                \GislerCMS\Controller\Admin\Misc\System\BackupController::class,
                \GislerCMS\Controller\Admin\Misc\System\UpdateController::class,
                \GislerCMS\Controller\Admin\Misc\System\MigrationController::class,
                \GislerCMS\Controller\Admin\Misc\System\ChangelogController::class,
                \GislerCMS\Controller\Admin\Module\AddController::class,
                \GislerCMS\Controller\Admin\Module\ListController::class,
                \GislerCMS\Controller\Admin\Module\ManageController::class
            ],
            'require_nologin' => [
                \GislerCMS\Controller\Admin\Auth\LoginController::class,
                \GislerCMS\Controller\Admin\Auth\ForgotPasswordController::class,
                \GislerCMS\Controller\Admin\Auth\ResetController::class
            ]
        ],

        'module' => [],

        'data_cache' => __DIR__ . '/../cache/',
        'root_path' => __DIR__ . '/../'
    ],
];
