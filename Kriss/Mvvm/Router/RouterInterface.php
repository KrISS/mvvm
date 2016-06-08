<?php

namespace Kriss\Mvvm\Router;

interface RouterInterface {
    public function addResponse($name, $method, $pattern, $response);
    public function getResponse($method, $uri);
    public function generate($name, $params, $absolute);
}
