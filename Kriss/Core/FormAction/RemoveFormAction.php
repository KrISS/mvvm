<?php

namespace Kriss\Core\FormAction;

use Kriss\Mvvm\FormAction\FormActionInterface;

class RemoveFormAction implements FormActionInterface {
    use FormActionTrait;

    private function saveEntity($entity) {
        $this->model->remove($entity);
        $this->flush = true;
    }
    
    public function success($data) {$this->traitSuccess($data);}
    
    public function failure($data) {$this->traitFailure($data);}
}
