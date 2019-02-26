<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
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
        ]
    ],
];
