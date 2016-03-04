<?php

namespace Kriss\Rest\Router;

use \Kriss\Mvvm\Container\ContainerInterface;
use \Kriss\Mvvm\Router\RouterInterface;
use \Kriss\Mvvm\Model\ListModelInterface;

class AutoRoute {
    protected $container;
    protected $router;
    protected $classes;
    protected $prefix;
    protected $rules = [];

    public function __construct(ContainerInterface $container, RouterInterface $router, $classes = [])
    {
        $this->container = $container;
        $this->router = $router;
        $this->classes = $classes;
        $classes = join(array_keys($classes), '|');
        $this->prefix = '<class'.(empty($classes)?'':':'.$classes).'>';
        $this->addResponses();
    }

    public function addResponses()
    {
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/', 'index');
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/new/', 'new');
        $this->addResponse($this->router, 'POST', '/'.$this->prefix.'/', 'create');
    }

    public function addResponse(RouterInterface $router, $method, $pattern, $name)
    {
        $autoroute = $this;
        $router->addResponse(
            'autoroute_'.$name, $method, $pattern,
            function ($class, $id = null) use ($autoroute, $name) {
                $fun = $autoroute->getFunction($name);
                $className = $autoroute->getClassName($class);
                call_user_func_array(array($autoroute, 'auto'.$fun), [$className, $id]);
                return $autoroute->getRouteResponse($name, $className);
            }
        );
    }

    protected function getRouteResponse($action, $className)
    {
        $class = $this->getClass($className);
        $autoRoute = '$autoRoute_' . $action . '_' . $class;

        if (!$this->container->has($className)) return false;

        if (!$this->container->has($autoRoute)) {
            $this->generate($action);
            $viewName = $className . '\\View';
            $controllerName = $className . '\\Controller';
            $this->container->set($autoRoute, [
                'instanceOf' => 'Kriss\\Core\\Response\\ViewControllerResponse',
                'constructParams' => [
                    ['instance' => $viewName],
                    ($this->container->has($controllerName)) ? ['instance' => $controllerName] : null,
                ]
            ]);
        }

        return $this->container->get($autoRoute);
    }

    protected function autoIndex($className)
    {
        $this->generateModel($className);
        $this->generateViewModel($className);
        $this->generateView($className);
    }

    protected function autoNew($className)
    {
        $this->generateModel($className);
        $this->generateFormViewModel($className, null, ['instance' => $className], 'POST');
        $this->generateFormView($className);
    }

    protected function autoCreate($className) {
        $this->autoNew($className);
        $this->generateValidator($className);
        $this->rules[$className.'\\ViewModel']['constructParams'][1] = ['instance' => $className.'\\Validator'];
        $this->generateFormController($className);
        $this->rules[$className.'\\Controller'] = $this->rules[$className.'\\FormController'];
        unset($this->rules[$className.'\\FormController']);
    }

    protected function generateModel($className)
    {
        $class = $this->getClass($className);
        
        $this->rules[$className.'\\Model'] = [
            'instanceOf' => 'Kriss\\Core\\Model\\Model',
            'constructParams' => [
                $class,
                $className,
                'data',
            ]
        ];
    }

    protected function generateViewModel($className)
    {
        $this->rules[$className.'\\ViewModel'] = [
            'instanceOf' => 'Kriss\\Core\\ViewModel\\ViewModel',
            'shared' => true,
            'constructParams' => [
                ['instance' => $className.'\\Model'],
            ]
        ];
    }

    protected function generateView($className)
    {
        $this->rules[$className.'\\View'] = [
            'instanceOf' => 'Kriss\\Rest\\View\\RouterView',
            'constructParams' => [
                ['instance' => $className.'\\ViewModel'],
                ['instance' => 'Router'],
            ]
        ];
    }

    protected function generateFormController($className)
    {
        $this->rules[$className.'\\FormController'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\FormController',
            'constructParams' => [
                ['instance' => $className.'\\ViewModel'],
                ['instance' => 'Kriss\\Core\\Request\\Request'],
            ]
        ];
    }

    protected function generateFormViewModel($className, $validator = null, $data = null, $method = 'POST')
    {
        $this->rules[$className.'\\ViewModel'] = [
            'instanceOf' => 'Kriss\\Core\\ViewModel\\FormViewModel',
            'shared' => true,
            'constructParams' => [
                ['instance' => $className.'\\Model'],
                $validator,
                $data,
                $method,
            ]
        ];
    }

    protected function generateFormView($className)
    {
        $this->rules[$className.'\\View'] = [
            'instanceOf' => 'Kriss\\Rest\\View\\RouterFormView',
            'constructParams' => [
                ['instance' => $className.'\\ViewModel'],
                ['instance' => 'Router'],
            ],
        ];
    }

    protected function generateValidator($className)
    {
        $this->rules[$className.'\\Validator'] = [
            'instanceOf' => 'Kriss\\Core\\Validator\\Validator',
        ];
    }

    protected function generateListController($className, $controllers = [])
    {
        $call = [];
        foreach($controllers as $controller) {
            $call[] = ['addController', [$className.'\\'.$controller]];
        }
        $this->rules[$className.'\\Controller'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\ListController',
            'constructParams' => [
                $this->container,
            ],
            'call' => $call,
        ];
    }

    protected function generate($action)
    {
        $action = $this->getFunction($action);
        foreach($this->rules as $className => $rule) {
            $fullClassName = explode('\\', $className);
            array_splice($fullClassName, 1, 0, [$action]);
            $fullClassName = implode('\\', $fullClassName);
            if (!$this->container->has($fullClassName)) {
                if (!$this->container->has($className)) {
                    $this->container->set($className, $rule);
                }
            } else {
                $this->container->set($className, $this->container->getRule($fullClassName));
            }
        }
    }

    protected function getClass($className)
    {
        $result = array_search($className, $this->classes);
        if ($result !== false) return $result;
        else return strtolower($className);
    }

    protected function getFunction($name)
    {
        return preg_replace_callback(
            '/(^|_)([a-z])/'
            , function ($matches) {
                return strtoupper($matches[2]);
            }
            , $name
        );
    }

    private function getClassName($class)
    {
        if (isset($this->classes[$class])) return $this->classes[$class];
        else return ucfirst($class);
    }
}
