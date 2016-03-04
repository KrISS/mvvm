<?php

namespace Kriss\Core\Validator;

use Kriss\Mvvm\Validator\ValidatorInterface;

class Validator implements ValidatorInterface {
    private $errors = [];
    private $constraints = [];

    public function setConstraints($constraints)
    {
        $this->constraints = $constraints;
    }

    public function isValid($data) {
        foreach($this->constraints as $constraint) {
            $constraint[] = $constraint[0].' Error';
            list($name, $rule, $params, $error) = $constraint;
            $value = $data->$name;

            switch($rule) {
            case 'email':
                if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                    $this->errors[$name][] = $error;
                }
                break;
            case 'minLength':
                if (strlen($value) <= $params[0]) {
                    $this->errors[$name][] = $error;
                }
                break;
            default:
                trigger_error(__METHOD__.': '.$rule.' validation is not defined');

                break;
            }
        }

        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }
}