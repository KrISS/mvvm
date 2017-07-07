<?php

namespace Kriss\Mvvm\FormAction;

interface FormActionInterface {
    public function success($data);
    public function failure($data);
}
