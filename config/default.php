<?php

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header
        'timezone' => 'Europe/Zurich',

        // Renderer settings
        'renderer' => [
            'template_paths' => [
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

        'version' => '1.0.1',

        'global' => [
            'admin_route' => '/admin'
        ],

        'classes' => [
            'default' => [
                \GislerCMS\Controller\Admin\Auth\LogoutController::class,
                \GislerCMS\Controller\Admin\SetupController::class,
                \GislerCMS\Controller\SitemapController::class,
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
                \GislerCMS\Controller\Admin\Page\TrashController::class,
                \GislerCMS\Controller\Admin\Page\EditController::class,
                \GislerCMS\Controller\Admin\Post\ListController::class,
                \GislerCMS\Controller\Admin\Post\TrashController::class,
                \GislerCMS\Controller\Admin\Post\EditController::class,
                \GislerCMS\Controller\Admin\Widget\ListController::class,
                \GislerCMS\Controller\Admin\Widget\TrashController::class,
                \GislerCMS\Controller\Admin\Widget\EditController::class,
                \GislerCMS\Controller\Admin\Misc\ConfigController::class,
                \GislerCMS\Controller\Admin\Misc\SysInfoController::class,
                \GislerCMS\Controller\Admin\Misc\ProfileController::class,
                \GislerCMS\Controller\Admin\Misc\ChangePasswordController::class,
                \GislerCMS\Controller\Admin\Misc\MigrationController::class,
                \GislerCMS\Controller\Admin\Misc\Language\ListController::class,
                \GislerCMS\Controller\Admin\Misc\Language\EditController::class,
                \GislerCMS\Controller\Admin\Misc\User\ListController::class,
                \GislerCMS\Controller\Admin\Misc\User\EditController::class,
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

        'mailer' => [
            'smtp' => false,
            'host' => 'mail.example.com',
            'smtpauth' => true,
            'username' => 'max.muster@example.com',
            'password' => 'mypass',
            'smtpsecure' => 'ssl',
            'port' => 465,
            'default_from' => [
                'name' => 'GislerCMS',
                'email' => 'gislercms@example.com'
            ]
        ],

        'module' => [],

        'data_cache' => __DIR__ . '/../cache/'
    ],
];
