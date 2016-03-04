<?php

namespace Kriss\Core\Controller;

use Kriss\Mvvm\Controller\ControllerInterface;

use Kriss\Mvvm\ViewModel\FormViewModelInterface as ViewModel;
use Kriss\Mvvm\Request\RequestInterface as Request;

class FormController implements ControllerInterface {
    protected $viewModel;
    protected $request;
    
    public function __construct(ViewModel $viewModel, Request $request) {
        $this->viewModel = $viewModel;
        $this->request = $request;
    }
    
    public function action() {
        $data = $this->viewModel->setFormData($this->request->getRequest());
        if ($this->viewModel->isValid($data)) {
            $this->viewModel->success($data);
        } else {
            $this->viewModel->failure($data);
        }
    }
}
