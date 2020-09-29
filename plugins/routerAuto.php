<?php

use Kriss\Mvvm\Container\ContainerInterface;
use Kriss\Mvvm\Request\RequestInterface;
use Kriss\Mvvm\Router\RouterInterface;
use Kriss\Mvvm\View\ViewInterface;
use Kriss\Mvvm\ViewModel\ViewModelInterface;
use Kriss\Mvvm\ViewModel\FormViewModelInterface;

use Kriss\Core\Response\Response;

include_once('modelArray.php');

class RouterAutoFormView implements ViewInterface {
    protected $viewModel;
    protected $router;

    public function __construct(FormViewModelInterface $viewModel, RouterInterface $router) {
        $this->viewModel = $viewModel;
        $this->router = $router;
    }
    
    protected function stringify($data) {
        $result = '';
        if (!empty($data)) {
            $string = ['<ul>'];
            foreach($data as $key => $item) {
                $attr = '';
                if (is_object($item) || is_array($item)) {
                    $string[] = '<li'.$attr.'>'.$key.': '.$this->stringify($item).'</li>';
                } else {
                    $string[] = '<li'.$attr.'>'.$key.': '.$item.'</li>';
                }
            }
            $string[] = '</ul>';
            $result = join('', $string);
        }

        return $result;
    }

    protected function renderName($name, $value) {
        $result = '';
        if ($name != 'id' && $name[0] != '*') {
            $attrs = '';
            $label = $name;
            if (isset($value['attrs'])) {
                foreach($value['attrs'] as $key => $val) {
                    $attrs .= ' '.$key.'="'.$val.'"';
                }
            }
            if (isset($value['label'])) {
                $label = $value['label'];
            }            
            switch($value['type']) {
            case 'textarea':
                $result = '<div><label>'.$label.': <br><textarea name="'.$name.'"'.$attrs.'>'.$value['value'].'</textarea></label></div>';
                break;
            default:
                $result = '<div><label>'.$label.': <input name="'.$name.'" value="'.$value['value'].'" type="'.$value['type'].'"'.$attrs.'/></label></div>';
            }
        }
        return $result;
    }
    
    protected function renderForm($slug, $object) {
        $method = $object['*']['method'];
        $url = $object['*']['action'];
        $result = '<form action="'.$url.'" id="'.$slug.'" method="'.$method.'">';
        if (isset($object['_method']['value'])) $method = $object['_method']['value'];
        if ($method != 'DELETE') {
            foreach($object as $name => $value) {
                $result .= $this->renderName($name, $value);
            }
        } else {
            foreach($object as $name => $value) {
                if ($name != 'id' && $name[0] != '*') {
                    if ($name === '_method') {
                        $result .= '<input name="'.$name.'" value="'.$value['value'].'" type="'.$value['type'].'"/>';
                    } else {
                        $result .= '<div>'.$name.': '.$value['value'].'</div>';
                    }
                }
            }
        }
        $result .= '<input type="submit" value="'.$method.'"/>';
        $result .= '</form>';
        return $result;
    }
    
    public function render() {
        $result = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>KrISS MVVM</title></head><body>';

        $data = $this->viewModel->getData();
        $data = [$data['slug'] => $data['form']];
        $errors = $this->viewModel->getErrors();
        
        $result .= $this->stringify($errors);
        
        if (!is_null($data)) {
            foreach($data as $slug => $object) {
                $result .= '<ul><li><a href="'.$this->router->generate('autoroute_index', ['slug' => $slug]).'">'.$slug.'</a></li></ul>';
            }
            foreach($data as $slug => $object) {
                if (!is_null($object)) {
                    $result .= $this->renderForm($slug, $object);
                }
            }
        }
        $result .= '</html>';
        return [[], $result];
    }
}

class RouterAutoView implements ViewInterface {
    private $viewModel;
    private $request;
    private $router;
    
    public function __construct(ViewModelInterface $viewModel, RouterInterface $router, RequestInterface $request) {
         $this->viewModel = $viewModel;
         $this->request = $request;
         $this->router = $router;
    }
    
    private function classToAttr($class) {
        return strtolower($class);
    }

    private function pagination($slug, $pagination) {
        $current = $pagination['current'];
        $total = $pagination['total'];
        $string = [];
        if ($total > 1) {
            $route = 'autoroute_index';
            $string[] = '<ul id="pagination-'.$slug.'" class="pagination">';
            
            if ($current > 1) {
                $page = $current - 1;
                $url = $this->router->generate($route, array_merge($this->request->getQuery(), ['slug' => $slug, 'page' => $page]));
                $string[] = '<li class="previous"><a href="'.$url.'">previous</a></li>';
            }
            $page = $current;
            $string[] = '<li class="current">'.$page.'/'.$total.'</li>';
            if ($current < $total) {
                $page = $current + 1;
                $url = $this->router->generate($route, array_merge($this->request->getQuery(), ['slug' => $slug, 'page' => $page]));
                $string[] = '<li class="next"><a href="'.$url.'">next</a></li>';
            }
            $string[] = '</ul>';
        }
        return join('', $string);
    }

    private function data($slug, $data) {
        $string[] = '<ul>';
        foreach($data as $key => $item) {
            if ($key == 'pagination') { $string[] = 'pagination';}
            if ($key == 'slug') { $string[] = 'slug';}
            $attr = is_object($item)?$this->classToAttr(get_class($item)):(!is_numeric($key)?$key:'');
            $attr = empty($attr)?'':($slug === true?' id="'.$attr.'"':' class="'.$attr.'"');
            
            if (is_object($item) || is_array($item)) {
                $routeIndex = 'autoroute_index_id';
                $routeEdit = 'autoroute_edit_id';
                $routeDelete = 'autoroute_delete_id';
                try {
                    $urlIndex = $this->router->generate($routeIndex, ['slug' => $slug, 'id' => $key]);
                    $urlEdit = $this->router->generate($routeEdit, ['slug' => $slug, 'id' => $key]);
                    $urlDelete = $this->router->generate($routeDelete, ['slug' => $slug, 'id' => $key]);
                } catch (\Exception $e) {
                    $routeIndex = 'autoroute_index';
                    $routeEdit = 'autoroute_edit';
                    $routeDelete = 'autoroute_delete';
                    $urlIndex = $this->router->generate($routeIndex, ['slug' => $slug]);
                    $urlEdit = $this->router->generate($routeEdit, ['slug' => $slug]);
                    $urlDelete = $this->router->generate($routeDelete, ['slug' => $slug]);
                }
                $string[] = '<li'.$attr.'><a href="'.$urlIndex.'">'.$key.'</a>: <a href="'.$urlEdit.'">edit</a> <a href="'.$urlDelete.'">delete</a> '.$this->data($slug, $item).'</li>';
            } else {
                // TODO: remove using form configuration ?
                if ($key != 'password') {
                    $string[] = '<li'.$attr.'>'.$key.': '.$item.'</li>';
                }
            }            
        }
        $string[] = '</ul>';
        return join('', $string);
    }
    
    private function stringify($data) {
        return
            '<!DOCTYPE html><html><head><meta charset="utf-8"><title>KrISS MVVM</title></head><body>'.
            '<a href="'.$this->request->getSchemeAndHttpHost().$this->request->getBaseUrl().'">index</a>'.
            (isset($data['pagination'])?$this->pagination($data['slug'], $data['pagination']):'').
            '<ul id="'.$data['slug'].'">'.
            '<li><a href="'.$this->router->generate('autoroute_index', ['slug' => $data['slug']]).'">'.$data['slug'].'</a>: <a href="'.$this->router->generate('autoroute_edit', array_merge($this->request->getQuery(), ['slug' => $data['slug']])).'">edit</a> <a href="'.$this->router->generate('autoroute_new', ['slug' => $data['slug']]).'">new</a>'.
            $this->data($data['slug'], $data['data']).'</li>'.
            '</ul></html>';
        
    }
    
    public function render() {return [[], $this->stringify($this->viewModel->getData())];}
}


class AutoRoute {
    protected $classes;
    protected $singleClasses;
    protected $listClasses;
    protected $prefix;
    protected $prefixId;
    protected $resetModel = false;
    protected $rules = [];
    
    public function __construct(ContainerInterface $container, $autoSingleClasses = [], $autoListClasses = []) {
        $this->container = $container;
        $this->request = $container->get('Request');
        $this->router = $container->get('Router');
        $this->singleClasses = $autoSingleClasses;
        $this->listClasses = $autoListClasses;
        
        $this->classes = array_merge($this->singleClasses, $this->listClasses);
        modelArray($this->container, $this->classes);
        $slugs = join(array_merge(array_filter(array_keys($this->singleClasses), 'is_string'),array_filter(array_keys($this->listClasses), 'is_string')), '|');
        $slugsId = join(array_filter(array_keys($this->listClasses), 'is_string'), '|');
        $this->prefix = '<slug'.(empty($slugs)?'':':'.$slugs).'>';
        $this->prefixId = '<slug'.(empty($slugsId)?'':':'.$slugsId).'>';
        $this->addResponses();
    }
    
    private function addResponses() {
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/', 'index');
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/new/', 'new');
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/edit/', 'edit');
        $this->addResponse($this->router, 'POST', '/'.$this->prefix.'/', 'create');
        $this->addResponse($this->router, 'PUT', '/'.$this->prefix.'/', 'update');
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/delete/', 'delete');
        $this->addResponse($this->router, 'DELETE', '/'.$this->prefix.'/', 'remove');
        $this->addResponse($this->router, 'GET', '/'.$this->prefixId.'/<id:\d+>/', 'index_id');
        $this->addResponse($this->router, 'GET', '/'.$this->prefixId.'/<id:\d+>/edit/', 'edit_id');
        $this->addResponse($this->router, 'PUT', '/'.$this->prefixId.'/<id:\d+>/', 'update_id');
        $this->addResponse($this->router, 'GET', '/'.$this->prefixId.'/<id:\d+>/delete/', 'delete_id');
        $this->addResponse($this->router, 'DELETE', '/'.$this->prefixId.'/<id:\d+>/', 'remove_id');
    }
    
    private function addResponse(RouterInterface $router, $method, $pattern, $action) {
        $autoroute = $this;
        $router->setRoute(
            'autoroute_'.$action, $method, $pattern,
            function ($slug, $id = null) use ($autoroute, $action) {
                $fun = $autoroute->getFunction($action);
                $autoroute->resetModel = false;
                call_user_func_array(array($autoroute, 'auto'.$fun), [$slug, $action, $id]);
                return $autoroute->getRouteResponse($slug, $action);
            }
        );
    }
    
    private function getRouteResponse($slug, $action) {
        $autoRoute = '#auto_route_' . $slug . '_' . $action;
        
        $class = $this->getClass($slug);
        if (!is_null($class) && !$this->container->has($class)) return new Response('Invalid autoroute: '.$autoRoute);
        
        if (!$this->container->has($autoRoute)) {
            $this->generate($slug, $action);
        }
        
        if (!$this->container->has($autoRoute)) {
            $this->generate($slug, $action);
            $this->container->set($autoRoute, [
                'instanceOf' => 'Kriss\\Core\\Response\\ViewControllerResponse'
            ]);
        }
        
        $model = $this->container->get('#'.$slug.'_model');
        $form = null;
        if ($this->container->has('#'.$slug.'_'.$action.'_form')) {
            $params = array_merge($this->router->getRouteParameters(),$this->request->getQuery());
            $method = 'POST';

            switch($action) {
            case 'edit':
            case 'update':
            case 'edit_id':
            case 'update_id':
                $method = 'PUT';
                break;
            case 'delete':
            case 'remove':
            case 'delete_id':
            case 'remove_id':
                $method = 'DELETE';
                break;
                
            }
            $form = $this->container->get('#'.$slug.'_'.$action.'_form', [$this->container->get($this->getClass($slug)), $method, $this->router->generate('autoroute_'.(isset($params['id'])?'index_id':'index'), $params)]);
        }
        $validator = null;
        if ($this->container->has('#'.$slug.'_'.$action.'_validator'))
            $validator = $this->container->get('#'.$slug.'_'.$action.'_validator');
        $formAction = null;
        if ($this->container->has('#'.$slug.'_'.$action.'_form_action'))
            $formAction = $this->container->get('#'.$slug.'_'.$action.'_form_action', [$model, $form, $this->request, $this->resetModel]);
        $viewModel = $this->container->get('#'.$slug.'_'.$action.'_view_model', [$model, $form, $validator]);
        $controller = null;
        if ($this->container->has('#'.$slug.'_'.$action.'_controller')){
            $rule = $this->container->getRule('#'.$slug.'_'.$action.'_controller');
            
            switch($rule['instanceOf']) {
            case 'Kriss\\Core\\Controller\\ListController':
                $controller = $this->container->get('#'.$slug.'_'.$action.'_controller', [$viewModel, $this->request, $this->router]);
                break;
            case 'Kriss\\Core\\Controller\\FormController':
                $controller = $this->container->get('#'.$slug.'_'.$action.'_controller', [$viewModel, $this->request, $formAction]);
                break;
            case 'Kriss\\Core\\Controller\\FormListController':
                $controller = $this->container->get('#'.$slug.'_'.$action.'_controller', [$viewModel, $this->request, $formAction, $this->router]);
                break;
            }
        }
        $view = $this->container->get('#'.$slug.'_'.$action.'_view', [$viewModel, $this->router, $this->request]);
        return $this->container->get($autoRoute, [$view, $controller]);
    }
    
    private function getFunction($name) {
        return preg_replace_callback(
            '/(^|_)([a-z])/'
            , function ($matches) {
                return strtoupper($matches[2]);
            }
            , $name
        );
    }
    
    private function _autoEdit($slug, $action, $id = null) {
        $this->generateModel($slug, $action);
        $this->generateForm($slug, $action, 'PUT');
        $this->generateFormViewModel($slug, $action, null);
        $this->generateFormView($slug, $action);
        $this->generateListController($slug, $action);
    }
    
    private function _autoUpdate($slug, $action, $id = null) {
        $this->_autoEdit($slug, $action, $id);
        $this->generateValidator($slug, $action);
        $this->generatePersistFormAction($slug, $action);
        $this->generateFormListController($slug, $action);
    }
    
    private function _autoDelete($slug, $action, $id = null) {
        $this->_autoEdit($slug, $action, $id);
        $this->generateForm($slug, $action, 'DELETE');
    }
    
    private function _autoRemove($slug, $action, $id = null) {
        $this->_autoDelete($slug, $action, $id);
        $this->generateValidator($slug, $action);
        $this->generateFormListController($slug, $action);
    }
    
    private function autoIndex($slug, $action, $id = null) {
        $this->generateModel($slug, $action);
        $this->generateViewModel($slug, $action, $id);
        $this->generateView($slug, $action);
        $this->generateListController($slug, $action);
    }
    
    private function autoIndexId($slug, $action, $id) {$this->autoIndex($slug, $action, $id);}
    
    private function autoNew($slug, $action) {
        $this->generateModel($slug, $action);
        $this->generateForm($slug, $action, 'POST');
        $this->generateFormViewModel($slug, $action, null);
        $this->generateFormView($slug, $action);
    }
    
    private function autoCreate($slug, $action) {
        $this->autoNew($slug, $action);
        if (in_array($slug, array_keys($this->singleClasses))) {
            $this->resetModel = true;
        }
        $this->generateValidator($slug, $action);
        $this->generatePersistFormAction($slug, $action);
        $this->generateFormController($slug, $action);
    }
    
    private function autoEdit($slug, $action) {$this->_autoEdit($slug, $action);}
    
    private function autoEditId($slug, $action, $id) {$this->_autoEdit($slug, $action, $id);}
    
    private function autoUpdate($slug, $action) {$this->_autoUpdate($slug, $action);}
    
    private function autoUpdateId($slug, $action, $id) {$this->_autoUpdate($slug, $action, $id);}
    
    private function autoDelete($slug, $action) {$this->_autoDelete($slug, $action);}
    
    private function autoRemove($slug, $action) {
        $this->_autoRemove($slug, $action);
        $this->generateRemoveFormAction($slug, $action);
    }
    
    private function autoDeleteId($slug, $action, $id) {$this->_autoDelete($slug, $action, $id);}
    
    private function autoRemoveId($slug, $action, $id) {
        $this->_autoRemove($slug, $action, $id);
        $this->generateRemoveFormAction($slug, $action);
    }
    
    private function generateModel($slug, $action) {
        $this->rules[$slug][$action]['model'] = [
            'instanceOf' => 'Kriss\\Core\\Model\\ArrayModel',
            'shared' => true,
            'constructParams' => [
                $slug,
                $this->getClass($slug)
            ]
        ];
    }
    
    private function generateViewModel($slug, $action, $id = null) {
        $this->rules[$slug][$action]['view_model'] = [
            'instanceOf' => 'Kriss\\Core\\ViewModel\\ViewModel',
        ];
    }
    
    private function generateView($slug, $action) {
        $this->rules[$slug][$action]['view'] = [
            'instanceOf' => 'RouterAutoView',
        ];
    }
    
    private function generateListController($slug, $action) {
        $this->rules[$slug][$action]['controller'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\ListController',
        ];
    }
    
    private function generateForm($slug, $action, $method) {
        $this->rules[$slug][$action]['form'] = [
            'instanceOf' => 'Kriss\\Core\\Form\\Form',
        ];
    }
    
    private function generateFormViewModel($slug, $action, $validator = null) {
        $this->rules[$slug][$action]['view_model'] = [
            'instanceOf' => 'Kriss\\Core\\ViewModel\\FormViewModel',
        ];
    }
    
    private function generateFormView($slug, $action) {
        $this->rules[$slug][$action]['view'] = [
            'instanceOf' => 'RouterAutoFormView',
        ];
    }
    
    private function generatePersistFormAction($slug, $action) {
        $this->rules[$slug][$action]['form_action'] = [
            'instanceOf' => 'Kriss\\Core\\FormAction\\PersistFormAction',
        ];
    }
    
    private function generateRemoveFormAction($slug, $action) {
        $this->rules[$slug][$action]['form_action'] = [
            'instanceOf' => 'Kriss\\Core\\FormAction\\RemoveFormAction',
        ];
    }
    
    private function generateFormListController($slug, $action) {
        $this->rules[$slug][$action]['controller'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\FormListController',
        ];
    }
    
    private function generateFormController($slug, $action) {
        $this->rules[$slug][$action]['controller'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\FormController',
        ];
    }
    
    private function generateValidator($slug, $action) {
        $this->rules[$slug][$action]['validator'] = [
            'instanceOf' => 'Kriss\\Core\\Validator\\Validator',
        ];
    }
    
    private function generate($slug, $action) {
        foreach($this->rules[$slug][$action] as $key => $rule) {
            $classKey = '$'.strtolower($this->getClass($slug)).'_'.$key;
            $classActionKey = '$'.strtolower($this->getClass($slug)).'_'.$action.'_'.$key;
            $identifierKey = '#'.$slug.'_'.$key;
            $identifierActionKey = '#'.$slug.'_'.$action.'_'.$key;

            if ($this->container->has($classKey)) {
                $rule = array_replace_recursive($rule, $this->container->getRule($classKey));
            }
            
            if ($this->container->has($classActionKey)) {
                $rule = array_replace_recursive($rule, $this->container->getRule($classActionKey));
            }
            
            if ($this->container->has($identifierKey)) {
                $rule = array_replace_recursive($rule, $this->container->getRule($identifierKey));
            }
            
            if ($this->container->has($identifierActionKey)) {
                $rule = $this->container->getRule($identifierActionKey);
            }
            
            if ($key == 'model') {
                $this->container->set($identifierKey, $rule);
            } else {
                $this->container->set($identifierActionKey, $rule);
            }
        }
    }
    
    private function getClass($slug) {
        if (array_key_exists($slug, $this->classes)) return $this->classes[$slug];
        else return strtolower($slug);
    }
}

function routerAuto($app, $next = null, $singleClasses = [], $listClasses = []) {
    if (!is_null($next)) {
        return function() use ($app, $next, $singleClasses, $listClasses) {
            $container = $app->getContainer();
            
            $routerRule = $container->getRule('Router');
            $routerRule = isset($routerRule['call'])?$routerRule['call']:[];
            $newRule = [
                'setRoute', [
                    'index', 'GET', '/',
                    function () use ($container, $singleClasses, $listClasses) {
                        $router = $container->get('Router');
                        $request = $container->get('Request');
                        $body = '';
                        if (is_array($singleClasses)) {
                            foreach($singleClasses as $slug => $class) {
                                $body .= '<a href="'.$router->generate('autoroute_index', ['slug' => $slug]).'">'.$class.' ('.$slug.')</a><br>';
                            }
                        }
                        if (is_array($listClasses)) {
                            foreach($listClasses as $slug => $class) {
                                $body .= '<a href="'.$router->generate('autoroute_index', ['slug' => $slug], $request).'">'.$class.' ('.$slug.')</a><br>';
                            }
                        }
                        
                        return new Response($body);
                    }
                ]];
            $addNewRule = true;
            foreach ($routerRule as $rule) {
                if ($rule[0] === 'setRoute' && $rule[1][2] == '/' ) {
                    $addNewRule = false;
                }
            }
            if ($addNewRule) {
                $routerRule = array_merge($routerRule, [$newRule]);
            }
            
            $container->set('Router', [
                'instanceOf' => 'Kriss\\Core\\Router\\RequestRouter',
                'shared' => true,
                'constructParams' => [
                    ['instance' => 'Request'],
                ],
                'call' => $routerRule
            ]);

            new AutoRoute($container, $singleClasses, $listClasses);

            return $next();
        };
    }
}

if (isset($app)) $app->addPlugin('routerAuto');
