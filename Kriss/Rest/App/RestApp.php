<?php

namespace Kriss\Rest\App;

use Kriss\Mvvm\Container\ContainerInterface as Container;
use Kriss\Core\Response\Response;
use Kriss\Core\App\App;

class RestApp extends App
{
    public function __construct(Container $container, $autoSingleClasses = [], $autoListClasses = [])
    {
        parent::__construct($container);
        $container->set('Kriss\\Rest\\Router\\AutoSingleRoute', [
            'constructParams' => [
                $container,
                ['instance' => 'Router'],
                $autoSingleClasses,
            ]
        ]);
        $container->set('Kriss\\Rest\\Router\\AutoListRoute', [
            'constructParams' => [
                $container,
                ['instance' => 'Router'],
                $autoListClasses,
            ]
        ]);
        $container->set('Router', [
            'instanceOf' => 'Kriss\\Core\\Router\\Router',
            'shared' => true,
            'call' => [
                ['addResponse', [
                    'index', 'GET', '/',
                    function () use ($container, $autoSingleClasses, $autoListClasses) {
                        $router = $container->get('Router');
                        $body = '';
                        if (is_array($autoSingleClasses)) {
                            foreach($autoSingleClasses as $slug => $class) {
                                $body .= '<a href="'.$router->generate('autoroute_index', ['slug' => $slug], true).'">'.$class.' ('.$slug.')</a><br>';
                            }
                        }
                        if (is_array($autoListClasses)) {
                            foreach($autoListClasses as $slug => $class) {
                                $body .= '<a href="'.$router->generate('autoroute_index', ['slug' => $slug], true).'">'.$class.' ('.$slug.')</a><br>';
                            }
                        }

                        return new Response($body);
                    }
                ]],
            ]
        ]);
    }

    public function getContainer()
    {
        return $this->container;
    }

    public function run()
    {
        $this->container->get('Kriss\\Rest\\Router\\AutoSingleRoute');
        $this->container->get('Kriss\\Rest\\Router\\AutoListRoute');

        parent::run();
    }
}
