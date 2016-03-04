<?php

namespace Kriss\Rest\App;

use Kriss\Mvvm\Container\ContainerInterface as Container;
use Kriss\Core\Response\Response;
use Kriss\Core\App\App;

class RestApp extends App
{
    protected $timeStart;

    public function __construct(Container $container, $autoSingleClasses = [], $autoListClasses = [])
    {
        $this->timeStart = microtime(true);
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
                        $request = $container->get('Request');
                        $body = '';
                        foreach($autoSingleClasses as $class => $className) {
                            $body .= '<a href="'.$request->getHost().$router->generate('autoroute_index', ['class' => $class]).'">'.$className.'</a><br>';
                        }
                        foreach($autoListClasses as $class => $className) {
                            $body .= '<a href="'.$request->getHost().$router->generate('autoroute_index', ['class' => $class]).'">'.$className.'</a><br>';
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
        echo "<br>\n".'Total execution time in seconds: ' . (microtime(true) - $this->timeStart)."<br>\n";
    }
}
