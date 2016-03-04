<?php

namespace Kriss\Core\Controller;

use Kriss\Mvvm\Controller\ControllerInterface;

use Kriss\Mvvm\ViewModel\FormViewModelInterface as ViewModel;
use Kriss\Mvvm\Container\ContainerInterface as Container;

class ListController implements ControllerInterface {
    protected $container;
    protected $controllers = [];
    
    public function __construct(Container $container)
    {
        $this->container = $container;
    }
    
    public function addController($rule) {
        $this->controllers[] = $this->container->get($rule);
    }
    
    public function action() {
        foreach($this->controllers as $controller) {
            $controller->action();
        }
    }
}
