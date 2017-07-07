<?php

namespace Kriss\Demo;

use Kriss\Mvvm\View\ViewInterface;

class View implements ViewInterface {
    protected $viewModel;
    
    public function __construct(ViewModel $viewModel) {
        $this->viewModel = $viewModel;
    }
    
    public function render() {
        $data = $this->viewModel->getData();
        $errors = $this->viewModel->getErrors();
        $method = $data['method'];
        
        $result = $data['hello-world'];
        if (isset($errors['hello'])) {
            $result .= '<br>'.reset($errors['hello']);
        }
        $result .= '<form action="" method="'.$method.'"><input name="hello"/><input type="submit"/></form>Try "Tontof" or "T0nt0f"';
        
        return [[], $result];
    }
}
