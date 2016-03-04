<?php

namespace Kriss\Core\App;

use Kriss\Mvvm\Container\ContainerInterface as Container;
use Kriss\Mvvm\App\AppInterface;

class App implements AppInterface 
{
    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
        $this->container->set('Router', [
            'instanceOf' => 'Kriss\\Core\\Router\\Router',
            'shared' => true,
        ]);
        $this->container->set('Request', [
            'instanceOf' => 'Kriss\\Core\\Request\\Request',
            'shared' => true,
        ]);
        $this->container->set('Response', [
            'instanceOf' => 'Kriss\\Core\\Response\\Response',
            'shared' => true,
        ]);
        $this->container->set('Dispatcher', [
            'instanceOf' => 'Kriss\\Core\\Dispatcher\\Dispatcher',
            'shared' => true,
            'constructParams' => [
                ['instance' => 'Router'],
                ['instance' => 'Request'],
            ],
        ]);
     }

    public function getContainer()
    {
        return $this->container;
    }

    public function run()
    {
        $this->container->get('Dispatcher')->dispatch()->send();
    }
}