<?php
use App\Response;
use DI\ContainerBuilder;

include_once 'vendor/autoload.php';

include_once 'routes.php';
include_once 'app.php';

/*$redis = new Predis\Client([
    'scheme' => 'tcp',
    'host'   => 'redis',
    'port'   => 6379,
]);


$ads1 = [
    'text' => 'Продаётся автомобиль',
    'price' => 1000,
    'limit' => 3,
    'banner' => 'https://www.purina.ru/sites/default/files/2020-09/prichini.jpg',
];

$lastId = $redis->get('lastId');
if(empty($lastId)){
    $lastId = 0;
}
$ads1['id'] = ++$lastId;
$redis->hmset(md5(serialize($ads1)), $ads1);
$redis->set('lastId', $lastId);

$ads2 = [
    'text' => 'Продаётся дом',
    'price' => 500,
    'limit' => 20,
    'banner' => 'https://upload.wikimedia.org/wikipedia/commons/0/0e/Felis_silvestris_silvestris.jpg',
];

$lastId = $redis->get('lastId');
if(empty($lastId)){
    $lastId = 0;
}

$ads2['id'] = ++$lastId;


$result = $redis->hmset(md5(serialize($ads2)), $ads2);
$redis->set('lastId', $lastId);


$result = $redis->hgetall(md5(serialize($ads1)));
var_dump($result);
$result = $redis->hgetall(md5(serialize($ads2)));
var_dump($result);

$result = $redis->get('lastId');
var_dump($result);
die;*/


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
