<?php

return [
    'settings' => [
        'admin_navigation' => [
            [
                'items' => [
                    [
                        'route' => ['admin-index', 'admin-stats-clients', 'admin-stats-sessions', 'admin-stats-visits'],
                        'label' => 'Dashboard',
                        'icon' => 'fa-home',
                        'href' => '{admin_url}'
                    ],
                    [
                        'route' => 'admin-preview',
                        'label' => 'Seitenvorschau',
                        'icon' => 'fa-eye',
                        'href' => '{admin_url}/preview'
                    ],
                    [
                        'route' => 'admin-filemanager',
                        'label' => 'Dateiverwaltung',
                        'icon' => 'fa-folder-open',
                        'href' => '{admin_url}/filemanager'
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
                        'route' => [
                            'admin-misc-system-config',
                            'admin-misc-system-mailer',
                            'admin-misc-system-sysinfo',
                            'admin-misc-system-backup',
                            'admin-misc-system-update',
                            'admin-misc-system-migration',
                            'admin-misc-system-changelog'
                        ],
                        'label' => 'System',
                        'icon' => 'fa-cogs',
                        'href' => '{admin_url}/misc/system/config'
                    ],
                    [
                        'route' => ['admin-misc-language-list', 'admin-misc-language-edit'],
                        'label' => 'Sprachen',
                        'icon' => 'fa-language',
                        'href' => '{admin_url}/misc/language/list'
                    ],
                    [
                        'route' => ['admin-misc-user-list', 'admin-misc-user-edit'],
                        'label' => 'Benutzer',
                        'icon' => 'fa-users',
                        'href' => '{admin_url}/misc/user/list'
                    ],
                    [
                        'route' => 'admin-misc-trash',
                        'label' => 'Papierkorb',
                        'icon' => 'fa-trash-alt',
                        'href' => '{admin_url}/misc/trash',
                        'count' => '{trash_count}'
                    ],
                    [
                        'route' => 'admin-misc-license',
                        'label' => 'Lizenz',
                        'icon' => 'fa-info',
                        'href' => '{admin_url}/misc/license'
                    ]
                ]
            ]
        ]
    ]
];
