<?php

namespace Kriss\Mvvm\Router;

interface RouterInterface {
    public function dispatch($method, $pathInfo);
    public function generate($name, $params); 
    public function getRouteParameters();
    public function getRoutes($name);
    public function setRoute($name, $methods, $pattern, $response);
}

