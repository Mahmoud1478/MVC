<?php



$app = require_once '../bootstrap/Application.php';

$app->run();



dd(\Src\Routing\Router::getNamedRoutes());
