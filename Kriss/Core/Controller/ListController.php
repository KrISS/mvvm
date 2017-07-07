<?php

namespace Kriss\Core\Controller;

use Kriss\Mvvm\Controller\ControllerInterface;

use Kriss\Mvvm\ViewModel\ViewModelInterface;
use Kriss\Mvvm\Request\RequestInterface;
use Kriss\Mvvm\Router\RouterInterface;

class ListController implements ControllerInterface {
    use ListControllerTrait;
    
    protected $viewModel;
    protected $request;
    protected $router;
    
    public function __construct(ViewModelInterface $viewModel, RequestInterface $request, RouterInterface $router) {
        $this->viewModel = $viewModel;
        $this->request = $request;
        $this->router = $router;
    }
    
    public function action() {$this->listAction();}
}
