<?php

namespace Kriss\Demo;

use Kriss\Mvvm\Controller\ControllerInterface;

class Controller implements ControllerInterface {
    protected $viewModel;
    protected $request;

    public function __construct(ViewModel $viewModel, Request $request) {
	$this->viewModel = $viewModel;
        $this->request = $request;
    }

    public function action() {
        $data = $this->viewModel->setFormData($this->request->getQuery());
        if ($this->viewModel->isValid($data)) {
            $this->viewModel->success($data);
        } else {
            $this->viewModel->failure($data);
        }
    }
}
