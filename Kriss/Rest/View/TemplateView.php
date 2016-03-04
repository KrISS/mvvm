<?php

namespace Kriss\Core\View;

use Kriss\Mvvm\View\ViewInterface;

use Kriss\Mvvm\ViewModel\ViewModelInterface as ViewModel;

class TemplateView implements ViewInterface {
    protected $viewModel;
    protected $template;
    
    public function __construct(ViewModel $viewModel, $template = '') {
        $this->viewModel = $viewModel;
        $this->template = $template;
    }
    
    public function render() {
        $data = $this->viewModel->getData();
        ob_start();
        include($this->template);
        return [[], ob_get_clean()];
    }
}
