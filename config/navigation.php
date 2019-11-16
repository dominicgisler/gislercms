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
                'link' => [
                    'icon' => 'fa fa-plus',
                    'href' => '{admin_url}/page/add'
                ],
                'items' => [
                    [
                        'route' => ['admin-page-list', 'admin-page-edit'],
                        'label' => 'Alle Seiten',
                        'icon' => 'fa-copy',
                        'href' => '{admin_url}/page/list',
                        'count' => '{pages_count}'
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
                'title' => 'Beiträge',
                'link' => [
                    'icon' => 'fa fa-plus',
                    'href' => '{admin_url}/post/add'
                ],
                'items' => [
                    [
                        'route' => ['admin-post-list', 'admin-post-edit'],
                        'label' => 'Alle Beiträge',
                        'icon' => 'fa-copy',
                        'href' => '{admin_url}/post/list',
                        'count' => '{posts_count}'
                    ],
                    [
                        'route' => 'admin-post-trash',
                        'label' => 'Papierkorb',
                        'icon' => 'fa-trash-alt',
                        'href' => '{admin_url}/post/trash',
                        'count' => '{posts_trash_count}'
                    ]
                ]
            ],
            [
                'title' => 'Widgets',
                'link' => [
                    'icon' => 'fa fa-plus',
                    'href' => '{admin_url}/widget/add'
                ],
                'items' => [
                    [
                        'route' => ['admin-widget-list', 'admin-widget-edit'],
                        'label' => 'Alle Widgets',
                        'icon' => 'fa-copy',
                        'href' => '{admin_url}/widget/list',
                        'count' => '{widgets_count}'
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
                'link' => [
                    'icon' => 'fa fa-plus',
                    'href' => '{admin_url}/module/add'
                ],
                'items' => [
                    [
                        'route' => ['admin-module-list', 'admin-module-manage'],
                        'label' => 'Alle Module',
                        'icon' => 'fa-copy',
                        'href' => '{admin_url}/module/list',
                        'count' => '{modules_count}'
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