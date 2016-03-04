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

    public function setId($id) {}

    public function setFormData($data) { $this->data = $data; return $data; }

    public function getFormData() { return $this->data; }

    public function getData() {
        return [$this->model->getSlug() => $this->model->getData()];
    }

    public function getAction() { return 'GET'; }

    public function getErrors() { return $this->validator->getErrors(); }

    public function isValid($data) {        
        return $this->validator->isValid($data);
    }

    public function failure($data) { $this->model->remove(null); }

    public function success($data) { $this->model->persist($data['hello']); }
}
