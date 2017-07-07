<?php

namespace Kriss\Core\View;

use Kriss\Mvvm\View\ViewInterface;

class JsonView implements ViewInterface {
    use ViewTrait;
    
    public function render() {
        return [[], json_encode($this->viewModel->getData())];
    }
}
