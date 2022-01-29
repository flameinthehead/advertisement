<?php

namespace App;

use App\Exceptions\RouteException;

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
                $controller = new $class();
                return new Response(Response::HTTP_OK, $controller->{$method}());
            }
        }

        return new Response(Response::HTTP_NOT_FOUND);
    }

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

    private function assertRoute($route)
    {
        return ($route['method'] == $this->method && preg_match($route['rule'], $this->uri));
    }
}
