<?php

return [
    'settings' => [
        'admin_navigation' => [
            [
                'items' => [
                    [
                        'route' => 'admin-index',
                        'label' => 'Dashboard',
                        'icon' => 'fa-home',
                        'href' => '{admin_url}'
                    ],
                    [
                        'route' => 'admin-preview',
                        'label' => 'Seitenvorschau',
                        'icon' => 'fa-eye',
                        'href' => '{admin_url}/preview'
                    ]
                ]
            ],
            [
                'class' => 'd-md-none',
                'items' => [
                    [
                        'route' => 'admin-misc-profile',
                        'label' => 'Profil',
                        'icon' => 'fa-user',
                        'href' => '{admin_url}/misc/profile'
                    ],
                    [
                        'route' => 'admin-misc-change-password',
                        'label' => 'Passwort ändern',
                        'icon' => 'fa-key',
                        'href' => '{admin_url}/misc/change-password'
                    ],
                    [
                        'label' => 'Abmelden',
                        'icon' => 'fa-sign-out-alt',
                        'href' => '{admin_url}/logout'
                    ]
                ]
            ],
            [
                'title' => 'Seiten',
                'items' => [
                    [
                        'route' => ['admin-page-list', 'admin-page-edit'],
                        'label' => 'Alle Seiten',
                        'icon' => 'fa-copy',
                        'href' => '{admin_url}/page/list',
                        'count' => '{pages_count}'
                    ],
                    [
                        'label' => 'Neue Seite',
                        'icon' => 'fa-plus',
                        'href' => '{admin_url}/page/add'
                    ],
                    [
                        'route' => 'admin-page-defaults',
                        'label' => 'Standardwerte',
                        'icon' => 'fa-cog',
                        'href' => '{admin_url}/page/defaults'
                    ],
                    [
                        'route' => 'admin-page-trash',
                        'label' => 'Papierkorb',
                        'icon' => 'fa-trash-alt',
                        'href' => '{admin_url}/page/trash',
                        'count' => '{pages_trash_count}'
                    ]
                ]
            ],
            [
                'title' => 'Widgets',
                'items' => [
                    [
                        'route' => ['admin-widget-list', 'admin-widget-edit'],
                        'label' => 'Alle Widgets',
                        'icon' => 'fa-copy',
                        'href' => '{admin_url}/widget/list',
                        'count' => '{widgets_count}'
                    ],
                    [
                        'label' => 'Neues Widget',
                        'icon' => 'fa-plus',
                        'href' => '{admin_url}/widget/add'
                    ],
                    [
                        'route' => 'admin-widget-trash',
                        'label' => 'Papierkorb',
                        'icon' => 'fa-trash-alt',
                        'href' => '{admin_url}/widget/trash',
                        'count' => '{widgets_trash_count}'
                    ]
                ]
            ],
            [
                'title' => 'Module',
                'items' => [
                    [
                        'route' => ['admin-module-list', 'admin-module-manage'],
                        'label' => 'Alle Module',
                        'icon' => 'fa-copy',
                        'href' => '{admin_url}/module/list',
                        'count' => '{modules_count}'
                    ],
                    [
                        'route' => 'admin-module-add',
                        'label' => 'Neues Modul',
                        'icon' => 'fa-plus',
                        'href' => '{admin_url}/module/add'
                    ]
                ]
            ],
            [
                'title' => 'Verschiedenes',
                'items' => [
                    [
                        'route' => 'admin-misc-config',
                        'label' => 'Konfiguration',
                        'icon' => 'fa-cogs',
                        'href' => '{admin_url}/misc/config'
                    ],
                    [
                        'route' => 'admin-misc-language-list',
                        'label' => 'Sprachen',
                        'icon' => 'fa-language',
                        'href' => '{admin_url}/misc/language/list'
                    ],
                    [
                        'route' => 'admin-misc-migration',
                        'label' => 'Datenbank-Update',
                        'icon' => 'fa-database',
                        'href' => '{admin_url}/misc/migration'
                    ],
                    [
                        'route' => 'admin-misc-sysinfo',
                        'label' => 'Systeminformation',
                        'icon' => 'fa-info-circle',
                        'href' => '{admin_url}/misc/sysinfo'
                    ]
                ]
            ]
        ]
    ]
];
