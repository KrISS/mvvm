<?php

namespace Kriss\Core\Controller;

use Kriss\Mvvm\Controller\ControllerInterface;

use Kriss\Mvvm\FormAction\FormActionInterface;
use Kriss\Mvvm\ViewModel\FormViewModelInterface;
use Kriss\Mvvm\Request\RequestInterface;
use Kriss\Mvvm\Router\RouterInterface;

class FormListController implements ControllerInterface {
    use ListControllerTrait;
    use FormControllerTrait;
    
    private $formAction;
    protected $viewModel;
    protected $request;
    protected $router;
    
    public function __construct(FormViewModelInterface $viewModel, RequestInterface $request, FormActionInterface $formAction, RouterInterface $router) {
        $this->formAction = $formAction;
        $this->viewModel = $viewModel;
        $this->request = $request;
        $this->router = $router;
    }
    
    public function action() {
        $this->listAction();
        $this->formAction();
    }
}
