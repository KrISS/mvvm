<?php

namespace Kriss\Demo;

use Kriss\Mvvm\Controller\ControllerInterface;

use Kriss\Mvvm\FormAction\FormActionInterface;
use Kriss\Mvvm\ViewModel\FormViewModelInterface;
use Kriss\Mvvm\Request\RequestInterface;

class Controller implements ControllerInterface {
    private $viewModel;
    private $request;
    private $formAction;

    public function __construct(FormViewModelInterface $viewModel, RequestInterface $request, FormActionInterface $formAction) {
        $this->viewModel = $viewModel;
        $this->request = $request;
        $this->formAction = $formAction;
    }

    public function action() {
        $data = $this->request->getQuery();
        if ($this->viewModel->isValid($data)) {
            $this->formAction->success($data);
        } else {
            $this->formAction->failure($data);
        }
    }
}
