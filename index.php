<?php

use App\Exceptions\AppException;

error_reporting(E_ALL);
include_once 'vendor/autoload.php';
include_once 'routes.php';
include_once 'app.php';

try {
    $router = new App\Router($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
    $app = new App($router);
    $app->run();
} catch (AppException $e) {
    die($e->getMessage());
}
