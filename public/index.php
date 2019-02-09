<?php

require __DIR__ . '/../vendor/autoload.php';

session_start();

$app = new GislerCMS\Application();
$app->run();
