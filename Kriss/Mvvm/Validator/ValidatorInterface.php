<?php

namespace Kriss\Mvvm\Validator;

interface ValidatorInterface {
    public function setConstraints($constraints);
    public function isValid($data);
    public function getErrors();
}