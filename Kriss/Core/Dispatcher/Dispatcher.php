<?php

namespace Kriss\Core\Dispatcher;

use Kriss\Mvvm\Request\RequestInterface;
use Kriss\Mvvm\Router\RouterInterface;
use Kriss\Mvvm\Dispatcher\DispatcherInterface;

class Dispatcher implements DispatcherInterface
{
    protected $router;
    protected $request;

    public function __construct(RouterInterface $router, RequestInterface $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    public function dispatch()
    {
        return $this->router->getResponse($this->request->getMethod(), $this->request->getUri());
    }
}