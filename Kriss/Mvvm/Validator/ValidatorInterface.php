<?php

namespace Kriss\Mvvm\Validator;

interface ValidatorInterface {
    public function setConstraints($name, $constraints);
    public function getConstraints($name);
    public function isValid($data);
    public function getErrors();
}