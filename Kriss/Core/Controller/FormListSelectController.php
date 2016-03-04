<?php

namespace Kriss\Core\Controller;

use Kriss\Mvvm\Controller\ControllerInterface;

use Kriss\Mvvm\ViewModel\FormListViewModelInterface as ViewModel;
use Kriss\Mvvm\Request\RequestInterface as Request;

class FormListSelectController extends ListSelectController {
    public function __construct(ViewModel $viewModel, $id) {
        parent::__construct($viewModel, $id);
    }
}
