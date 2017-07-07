<?php

namespace Kriss\Core\App;

use Kriss\Mvvm\Container\ContainerInterface;
use Kriss\Mvvm\App\AppInterface;

class App implements AppInterface {
    private $container;
    private $plugins = [];
    private $configs = [];
    
    public function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->container->set('Request', [
            'instanceOf' => 'Kriss\\Core\\Request\\Request',
            'shared' => true,
        ]);
        $this->container->set('Router', [
            'instanceOf' => 'Kriss\\Core\\Router\\RequestRouter',
            'shared' => true,
            'constructParams' => [
                ['instance' => 'Request'],
            ],
        ]);
    }

    public function getContainer() {return $this->container;}

    public function addPlugin($name) {
        call_user_func_array($name, [$this]);
        $this->plugins[] = $name;
    }

    public function configPlugin($name, $config) {$this->configs[$name] = $config;}

    public function run() {
        $container = $this->container;
        $next = function() use ($container) {
            $request = $container->get('Request');
            return $container->get('Router')->dispatch($request->getMethod(), $request->getPathInfo());
        };
        foreach($this->plugins as $plugin) {
            $params = [$this, $next];
            if (array_key_exists($plugin, $this->configs)) {
                $params = array_merge($params, $this->configs[$plugin]);
            }
            $next = call_user_func_array($plugin, $params);
        }
        $response = $next();
        $response->send();
    }
}
