<?php

use GislerCMS\Controller\Admin\AdminIndexController;
use GislerCMS\Controller\Admin\AdminPreviewController;
use GislerCMS\Controller\Admin\AdminSetupController;
use GislerCMS\Controller\Admin\Auth\AdminForgotPasswordController;
use GislerCMS\Controller\Admin\Auth\AdminLoginController;
use GislerCMS\Controller\Admin\Auth\AdminLogoutController;
use GislerCMS\Controller\Admin\Auth\AdminResetController;
use GislerCMS\Controller\Admin\Misc\AdminMiscChangePasswordController;
use GislerCMS\Controller\Admin\Misc\AdminMiscConfigController;
use GislerCMS\Controller\Admin\Misc\AdminMiscMigrationController;
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

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

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

        'version' => '0.5.0-dev',

        'global' => [
            'admin_route' => '/admin'
        ],

        'classes' => [
            'default' => [
                AdminLogoutController::class,
                AdminSetupController::class,
                IndexController::class,
                PageLangController::class,
                PageController::class
            ],
            'require_login' => [
                AdminIndexController::class,
                AdminPreviewController::class,
                AdminPageAddController::class,
                AdminPageDefaultsController::class,
                AdminPageEditController::class,
                AdminPageTrashController::class,
                AdminWidgetAddController::class,
                AdminWidgetEditController::class,
                AdminWidgetTrashController::class,
                AdminMiscConfigController::class,
                AdminMiscSysInfoController::class,
                AdminMiscProfileController::class,
                AdminMiscChangePasswordController::class,
                AdminMiscMigrationController::class
            ],
            'require_nologin' => [
                AdminLoginController::class,
                AdminForgotPasswordController::class,
                AdminResetController::class
            ]
        ],

        'mailer' => [
            'smtp' => true,
            'host' => 'mail.example.com',
            'smtpauth' => true,
            'username' => 'max.muster@example.com',
            'password' => 'mypass',
            'smtpsecure' => 'ssl',
            'port' => 465,
            'default_from' => [
                'name' => 'GislerCMS',
                'email' => 'max.muster@example.com'
            ]
        ]
    ],
];
