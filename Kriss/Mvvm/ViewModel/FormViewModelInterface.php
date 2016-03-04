<?php

namespace Kriss\Mvvm\ViewModel;

interface FormViewModelInterface extends ViewModelInterface {
    public function setFormData($data);
    public function isValid($data);
    public function failure($data);
    public function success($data);
    public function getErrors();
    public function getAction();
}
