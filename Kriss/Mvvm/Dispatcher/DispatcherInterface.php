<?php

namespace Kriss\Mvvm\Dispatcher;

use Kriss\Mvvm\Request\RequestInterface;

interface DispatcherInterface {
    public function dispatch();
}