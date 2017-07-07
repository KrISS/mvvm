<?php

namespace Kriss\Demo;

use Kriss\Mvvm\ViewModel\FormViewModelInterface as ViewModelInterface;

class ViewModel implements ViewModelInterface {
    protected $model;
    protected $data;

    public function __construct(Model $model, Validator $validator) {
        $this->model = $model;
        $this->validator = $validator;
        $this->data = null;
    }

    public function setCriteria($criteria) {}
    public function setOrderBy($orderBy) {}
    public function setOffset($offset) {}
    public function setLimit($limit) {}

    public function getData() {
        return [$this->model->getSlug() => $this->model->getData(), 'method' => 'GET'];
    }

    public function getErrors() { return $this->validator->getErrors(); }

    public function isValid($data) {        
        return $this->validator->isValid($data);
    }
}
