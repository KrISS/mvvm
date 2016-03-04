<?php

namespace Kriss\Core\Controller;

use Kriss\Mvvm\Controller\ControllerInterface;

use Kriss\Mvvm\ViewModel\ListViewModelInterface as ViewModel;
use Kriss\Mvvm\Request\RequestInterface as Request;

class ListSelectController implements ControllerInterface {
    protected $viewModel;
    protected $id;

    public function __construct(ViewModel $viewModel, $id) {
        $this->viewModel = $viewModel;
        $this->id = $id;
    }

    public function action() {
        $this->viewModel->setId($this->id);
    }
}
