<?php

namespace Kriss\Core\Router;

use Kriss\Mvvm\Router\RouterInterface;

class Router implements RouterInterface {
    use RouterTrait;

    public function generate($name, $params = []) {
        return $this->generateRelativeUrl($name, $params);
    }
}
