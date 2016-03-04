<?php

namespace Kriss\Demo;

use Kriss\Mvvm\Validator\ValidatorInterface;

class Validator implements ValidatorInterface {
    private $errors = [];

    public function setConstraints($constraints) { }

    public function isValid($data) {
	if (!isset($data['hello']) || empty($data['hello'])) {
            return false;
	}
        if (preg_match('/[^a-zA-Z\-]/', $data['hello'])) {
             $this->errors['hello'][] = 'Invalid name';

             return false;
        }

        return true;
    }

    public function getErrors() {
        return $this->errors;
    }
}
