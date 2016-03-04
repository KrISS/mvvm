<?php

namespace Kriss\Core\Controller;

use Kriss\Mvvm\Controller\ControllerInterface;

use Kriss\Mvvm\ViewModel\FormViewModelInterface as ViewModel;
use Kriss\Mvvm\Request\RequestInterface as Request;

class FormSelectController implements ControllerInterface {
    protected $viewModel;

    public function __construct(ViewModel $viewModel) {
        $this->viewModel = $viewModel;
    }

    public function action() {
        $data = $this->viewModel->getData();
        $this->viewModel->setFormData(reset($data));
    }
}
