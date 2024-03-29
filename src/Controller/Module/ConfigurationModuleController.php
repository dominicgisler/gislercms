<?php

namespace GislerCMS\Controller\Module;

use GislerCMS\Controller\Admin\Module\Manage\ConfigurationController;

/**
 * Class ConfigurationModuleController
 * @package GislerCMS\Controller\Module
 */
class ConfigurationModuleController extends AbstractModuleController
{
    /**
     * @var array
     */
    protected static array $exampleConfig = [
        [
            'label' => 'Startseite',
            'href' => 'home'
        ],
        [
            'label' => 'Kontakt',
            'href' => 'contact'
        ]
    ];

    /**
     * @var string
     */
    protected static string $manageController = ConfigurationController::class;
}