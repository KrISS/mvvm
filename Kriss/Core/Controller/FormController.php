<?php

namespace Kriss\Core\Controller;

use Kriss\Mvvm\Controller\ControllerInterface;

use Kriss\Mvvm\FormAction\FormActionInterface;
use Kriss\Mvvm\ViewModel\FormViewModelInterface;
use Kriss\Mvvm\Request\RequestInterface;

class FormController implements ControllerInterface {
    use FormControllerTrait;
    
    protected $viewModel;
    protected $request;
    private $formAction;
    
    public function __construct(FormViewModelInterface $viewModel, RequestInterface $request, FormActionInterface $formAction) {
        $this->viewModel = $viewModel;
        $this->request = $request;
        $this->formAction = $formAction;
    }
    
    public function action() {$this->formAction();}
}
