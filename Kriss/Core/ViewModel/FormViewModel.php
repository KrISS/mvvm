<?php

namespace Kriss\Core\ViewModel;

use Kriss\Mvvm\ViewModel\FormViewModelInterface;
use Kriss\Mvvm\Model\ModelInterface as Model;
use Kriss\Mvvm\Validator\ValidatorInterface as Validator;

class FormViewModel extends ViewModel implements FormViewModelInterface {
    protected $validator;
    protected $action;
    protected $formData;

    public function __construct(Model $model, Validator $validator = null, $data = null, $action = 'POST') {
        parent::__construct($model);
        $this->formData = $data;
        $this->action = $action;
        $this->validator = $validator;
	}

    public function setFormData($data) {
        if (!is_null($data)) {
            foreach ($data as $key => $value) {
                $this->formData->$key = $value;
            }
        } else {
            $this->formData = null;
        }

        return $this->formData;
    }

    public function getFormData() {
        return [$this->model->getSlug() => $this->formData];
    }

    public function getErrors() {
        return !is_null($this->validator)?$this->validator->getErrors():[];
    }

    public function getAction() {
        return $this->action;
    }

    public function isValid($data) {        
        return !is_null($this->validator)?$this->validator->isValid($data):true;
    }

    public function failure($data) {

    }

    public function success($data) { 
        $this->model->persist($data);
        $this->model->flush();
    }
}

