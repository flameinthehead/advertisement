<?php

namespace App;

use App\Exceptions\RouteException;

// система роутинга - правила добавляются в /routes.php
class Router
{
    const POSSIBLE_HTTP_METHODS = ['GET', 'POST', 'DELETE', 'PATCH'];

    private $method;
    private $uri;
    private $possibleRoutes;

    public function __construct($requestMethod = '', $requestUri = '')
    {
        $this->method = $requestMethod;
        $this->uri = $requestUri;
        $this->possibleRoutes = include 'routes.php';
    }

    // определения ближайшего подходящего роута, просто внедрение зависимостей (нет рекурсивной проверки конструкторов)
    // с использованием рефлексии для автоподгрузки валидаторов и прочих простых зависимостей
    public function resolve()
    {
        if(empty($this->possibleRoutes) || !is_array($this->possibleRoutes)){
            throw new RouteException('Invalid route list');
        }

        foreach($this->possibleRoutes as $route){
            if(!$this->isValidRoute($route)){
                throw new RouteException('Invalid route '.serialize($route));
            }

            if($this->assertRoute($route)){
                list($class, $method) = $route['route'];
                $reflection = new \ReflectionClass($class);
                $dependencies = $this->getDependencies($reflection);
                $controller = $reflection->newInstance($dependencies ?? null);
                return new Response(Response::HTTP_OK, $controller->{$method}());
            }
        }

        return new Response(Response::HTTP_NOT_FOUND,[
            'message' => 'Route not found',
            'code' => Response::HTTP_NOT_FOUND,
            'data' => new \stdClass(),
        ]);
    }

    // вынесли валидацию роутов в отдельный метод
    private function isValidRoute($route)
    {
        $hasAllFields = (!empty($route['method']) && !empty($route['rule']) && !empty($route['route']));
        if(!$hasAllFields){
            return false;
        }

        list($class, $method) = $route['route'];

        return (
            in_array($route['method'], self::POSSIBLE_HTTP_METHODS)
            && class_exists($class)
            && method_exists($class, $method)
        );
    }

    // подходит ли роут для запроса
    private function assertRoute($route)
    {
        return ($route['method'] == $this->method && preg_match($route['rule'], $this->uri));
    }

    // получаем зависимости конструктора контроллера
    private function getDependencies($reflection)
    {
        $params = $reflection->getConstructor()->getParameters();
        $dependencies = [];
        foreach ($params as $param) {
            if(!class_exists($param->getType()->getName())){
                throw new RouteException('Unknown class name '.$param->getType());
            }

            $dependencies = new ($param->getType()->getName());
        }

        return $dependencies;
    }
}
