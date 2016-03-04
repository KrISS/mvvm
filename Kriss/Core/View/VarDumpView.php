<?php

namespace Kriss\Core\View;

class VarDumpView extends View {
    public function render() {
        ob_start();
        var_dump($this->viewModel->getData());
        $body = ob_get_contents();
        ob_end_clean();
        return [[], nl2br(str_replace(' ', '&nbsp;', $body))];
    }
}
