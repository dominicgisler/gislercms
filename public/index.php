<?php

require __DIR__ . '/../vendor/autoload.php';

if (getenv('APPLICATION_ENV')) {
    $env = getenv('APPLICATION_ENV');
} elseif (file_exists(__DIR__ . '/../environment')) {
    $env = file_get_contents(__DIR__ . '/../environment');
} else {
    $env = 'production';
}
define('APPLICATION_ENV', $env);

session_start();

$app = new GislerCMS\Application();
$app->run();
