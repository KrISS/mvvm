<?php

namespace Kriss\Demo;

use Kriss\Mvvm\FormAction\FormActionInterface;

use Kriss\Mvvm\Model\ModelInterface;

class FormAction implements FormActionInterface {
    private $model;
    
    public function __construct(ModelInterface $model) {
        $this->model = $model;
    }
    
    public function failure($data) { $this->model->remove(null); }
    
    public function success($data) {
        $this->model->persist($data['hello']);
    }
}

