<?php

namespace Kriss\Mvvm\ViewModel;

interface FormViewModelInterface extends ViewModelInterface {
    public function getErrors();
    public function isValid($data);
}
