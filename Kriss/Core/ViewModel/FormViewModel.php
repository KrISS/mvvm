<?php

namespace Kriss\Core\ViewModel;

use Kriss\Mvvm\ViewModel\FormViewModelInterface;

use Kriss\Mvvm\Form\FormInterface;
use Kriss\Mvvm\Model\ModelInterface;
use Kriss\Mvvm\Validator\ValidatorInterface;

class FormViewModel implements FormViewModelInterface {
    use ViewModelTrait;
    
    protected $validator;
    protected $form;
    private $errors = [];

    public function __construct(ModelInterface $model, FormInterface $form, ValidatorInterface $validator = null) {
        $this->model = $model;
        $this->form = $form;
        $this->validator = $validator;
    }
    
    public function getData() {return ['slug' => $this->model->getSlug(), 'form' => $this->form->getForm()];}
    
    public function setCriteria($criteria) {
        $this->criteria = $criteria;
        $data = $this->model->findBy($this->criteria, $this->limit, $this->offset, $this->orderBy);
        if (count($data) === 1) $data = reset($data);
        $this->form->setData($data);
    }

    public function getErrors() {return $this->errors;}

    private function dataErrors($data) {
        if (!is_null($this->validator)) $this->validator->isValid($data);
        return is_null($this->validator)?[]:$this->validator->getErrors();
    }

    public function isValid($data) {
        if (array_key_exists('_', $data)) $data = $data['_'];
        if (empty(array_filter(array_keys($data), 'is_int'))) {
            $this->errors = $this->dataErrors($data);
        } else {
            $errors = [];
            foreach($data as $key => $item) {
                $errors = $this->dataErrors($item);
                if (!empty($errors)) $this->errors[$key] = $errors;
            }
        }

        return empty($this->errors);
    }
}

