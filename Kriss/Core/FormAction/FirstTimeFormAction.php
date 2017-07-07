<?php

namespace Kriss\Core\FormAction;

use Kriss\Mvvm\FormAction\FormActionInterface;

use Kriss\Mvvm\Form\FormInterface;
use Kriss\Mvvm\Model\ModelInterface;
use Kriss\Mvvm\Request\RequestInterface;

class FirstTimeFormAction implements FormActionInterface {
    use FormActionTrait;

    public function __construct(ModelInterface $model, FormInterface $form, RequestInterface $request, $resetData = false) {
        $this->model = $model;
        $this->form = $form;
        $this->request = $request;
        $this->resetData = false;
    }
    
    private function saveEntity($entity) {
        if ($this->model->count() === 0) {
            $this->model->persist($entity);
            $this->flush = true;
        }
    }
    
    public function success($data) {$this->traitSuccess($data);}
    
    public function failure($data) {$this->traitFailure($data);}
}
