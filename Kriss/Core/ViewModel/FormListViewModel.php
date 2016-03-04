<?php

namespace Kriss\Core\ViewModel;

use Kriss\Mvvm\ViewModel\FormListViewModelInterface;
use Kriss\Mvvm\Model\ModelInterface as Model;
use Kriss\Mvvm\Validator\ValidatorInterface as Validator;

class FormListViewModel extends FormViewModel implements FormListViewModelInterface {
    public function setId($id) {
        $this->formData = $this->model->findOneBy($id);
        if (!is_null($id) && !is_null($this->formData)) $this->formData->id = $id;
    }
}

