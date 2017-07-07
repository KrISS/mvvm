<?php

namespace Kriss\Core\Controller;

trait FormControllerTrait {
    private function formAction()
    {
        $data = $this->request->getRequest();
        if ($this->viewModel->isValid($data)) {$this->formAction->success($data);}
        else {$this->formAction->failure($data);}
    }
}
