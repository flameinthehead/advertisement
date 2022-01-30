<?php
use App\Response;
use DI\ContainerBuilder;

include_once 'vendor/autoload.php';

include_once 'routes.php';
include_once 'app.php';

try {
    $builder = new ContainerBuilder();
    $builder->addDefinitions('config/di.php');
    $container = $builder->build();

    $router = new App\Router($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI'], $container);
    $app = new App($router);
    $app->run();
} catch (\Exception $e) {
    $response = new Response(Response::HTTP_BAD_REQUEST,[
        'message' => $e->getMessage(),
        'code' => Response::HTTP_BAD_REQUEST,
        'data' => new \stdClass(),
    ]);
    $response->sendJson();
}
