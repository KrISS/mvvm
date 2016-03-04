<?php

namespace Kriss\Core\View;

class JsonView extends View {
    public function render() {
        return [[], json_encode($this->viewModel->getData())];
    }
}
