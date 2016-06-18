<?php

namespace Kriss\Rest\Router;

use \Kriss\Mvvm\Container\ContainerInterface;
use \Kriss\Mvvm\Router\RouterInterface;

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
        if (is_array($classes)) {
            $slugs = join(array_keys($classes), '|');
            $this->prefix = '<slug'.(empty($slugs)?'':':'.$slugs).'>';
            $this->addResponses();
            foreach($classes as $slug => $class) {
                if (!$this->container->has('$'.$slug.'_model')) {
                    $this->generateModel($slug, 'default');
                    $this->container->set('$'.$slug.'_model', $this->rules[$slug]['default']['model']);
                }
            }
        }
    }

    public function addResponses()
    {
        $this->addResponse($this->router, 'GET', '/', 'homepage');
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/', 'index');
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/new/', 'new');
        $this->addResponse($this->router, 'POST', '/'.$this->prefix.'/', 'create');
    }

    public function addResponse(RouterInterface $router, $method, $pattern, $action)
    {
        $autoroute = $this;
        $router->addResponse(
            'autoroute_'.$action, $method, $pattern,
            function ($slug, $id = null) use ($autoroute, $action) {
                $fun = $autoroute->getFunction($action);
                call_user_func_array(array($autoroute, 'auto'.$fun), [$slug, $action, $id]);
                return $autoroute->getRouteResponse($slug, $action);
            }
        );
    }

    protected function getRouteResponse($slug, $action)
    {
        $autoRoute = '$auto_route_' . $slug . '_' . $action;

        $class = $this->getClass($slug);
        if (!$this->container->has($class)) return false;

        if (!$this->container->has($autoRoute)) {
            $this->generate($slug, $action);
            $viewName = '$'.$slug.'_'.$action.'_view';
            $controllerName = '$'.$slug.'_'.$action.'_controller';
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

    protected function autoIndex($slug, $action)
    {
        $this->generateModel($slug, $action);
        $this->generateViewModel($slug, $action);
        $this->generateView($slug, $action);
    }

    protected function autoNew($slug, $action)
    {
        $this->generateModel($slug, $action);
        $this->generateFormViewModel($slug, $action, null, ['instance' => $this->getClass($slug)], 'POST');
        $this->generateFormView($slug, $action);
    }

    protected function autoCreate($slug, $action) {
        $this->autoNew($slug, $action);
        $this->generateValidator($slug, $action);
        $this->rules[$slug][$action]['view_model']['constructParams'][1] = ['instance' => '$'.$slug.'_'.$action.'_validator'];
        $this->generateFormController($slug, $action);
        $this->rules[$slug][$action]['controller'] = $this->rules[$slug][$action]['form_controller'];

    }

    protected function generateModel($slug, $action)
    {
        $this->rules[$slug][$action]['model'] = [
            'instanceOf' => 'Kriss\\Core\\Model\\Model',
            'constructParams' => [
                $slug,
                $this->getClass($slug)
            ]
        ];
    }

    protected function generateViewModel($slug, $action)
    {
        $this->rules[$slug][$action]['view_model'] = [
            'instanceOf' => 'Kriss\\Core\\ViewModel\\ViewModel',
            'shared' => true,
            'constructParams' => [
                ['instance' => '$'.$slug.'_'.$action.'_model'],
            ]
        ];
    }

    protected function generateView($slug, $action)
    {
        $this->rules[$slug][$action]['view'] = [
            'instanceOf' => 'Kriss\\Rest\\View\\RouterView',
            'constructParams' => [
                ['instance' => '$'.$slug.'_'.$action.'_view_model'],
                ['instance' => 'Router'],
            ]
        ];
    }

    protected function generateFormController($slug, $action)
    {
        $this->rules[$slug][$action]['form_controller'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\FormController',
            'constructParams' => [
                ['instance' => '$'.$slug.'_'.$action.'_view_model'],
                ['instance' => 'Request'],
            ]
        ];
    }

    protected function generateFormViewModel($slug, $action, $validator = null, $data = null, $method = 'POST')
    {
        $this->rules[$slug][$action]['view_model'] = [
            'instanceOf' => 'Kriss\\Core\\ViewModel\\FormViewModel',
            'shared' => true,
            'constructParams' => [
                ['instance' => '$'.$slug.'_'.$action.'_model'],
                $validator,
                $data,
                $method,
            ]
        ];
    }

    protected function generateFormView($slug, $action)
    {
        $this->rules[$slug][$action]['view'] = [
            'instanceOf' => 'Kriss\\Rest\\View\\RouterFormView',
            'constructParams' => [
                ['instance' => '$'.$slug.'_'.$action.'_view_model'],
                ['instance' => 'Router'],
            ],
        ];
    }

    protected function generateValidator($slug, $action)
    {
        $this->rules[$slug][$action]['validator'] = [
            'instanceOf' => 'Kriss\\Core\\Validator\\Validator',
        ];
    }

    protected function generateListController($slug, $action, $controllers = [])
    {
        $call = [];
        foreach($controllers as $controller) {
            $call[] = ['addController', [$controller]];
        }
        $this->rules[$slug][$action]['controller'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\ListController',
            'constructParams' => [
                $this->container,
            ],
            'call' => $call,
        ];
    }

    protected function generate($slug, $action)
    {
        foreach($this->rules[$slug][$action] as $key => $rule) {
            $classKey = '*'.strtolower($this->getClass($slug)).'_'.$key;
            $classActionKey = '*'.strtolower($this->getClass($slug)).'_'.$action.'_'.$key;
            $identifierKey = '$'.$slug.'_'.$key;
            $identifierActionKey = '$'.$slug.'_'.$action.'_'.$key;
            
            if ($this->container->has($classKey)) {
                $rule = $this->mergeRule($rule, $this->container->getRule($classKey));
            }

            if ($this->container->has($classActionKey)) {
                $rule = $this->mergeRule($rule, $this->container->getRule($classActionKey));
            }

            if ($this->container->has($identifierKey)) {
                $rule = $this->mergeRule($rule, $this->container->getRule($identifierKey));
            }

            if ($this->container->has($identifierActionKey)) {
                $rule = $this->container->getRule($identifierActionKey);
            }

            $this->container->set($identifierActionKey, $rule);
        }
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

    protected function getClass($slug)
    {
        if (isset($this->classes[$slug])) return $this->classes[$slug];
        else return ucfirst($slug);
    }

    protected function mergeRule($fromRule, $toRule) {
        foreach($toRule as $key => $value) {
            if (is_array($value) && isset($fromRule[$key])) {
                $fromRule[$key] = $this->mergeRule($fromRule[$key], $value);
            } else {
                $fromRule[$key] = $value;
            }
        }

        return $fromRule;
    }
}
