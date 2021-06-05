<?php

require __DIR__ . '/../vendor/autoload.php';

session_cache_limiter('public');
session_start();

$app = new GislerCMS\Application();
$app->run();
