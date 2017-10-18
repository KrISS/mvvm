<?php

namespace Kriss\Core\Validator;

use Kriss\Mvvm\Validator\ValidatorInterface;

class Validator implements ValidatorInterface {
    private $errors = [];
    private $constraints = [];

    private function getProperty($data, $name) {
        $value = null;
        if (is_array($data) && array_key_exists($name, $data)) {
            $value = $data[$name];
        }
        if (is_object($data) && isset($data->$name)) {
            $value = $data->$name;
        }
        
        return $value;
    }

    private function setConstraint($name, $rule, $params = [], $error = '') {
        
        if (empty($error)) $error = $name.' '.$rule.' Error';
        $this->constraints[$name][] = [$rule, $params, $error];
    }
    
    public function setConstraints($name, $constraints = []) {
        $this->constraints[$name] = [];
        foreach ($constraints as $constraint) {
            call_user_func_array(array($this, 'setConstraint'), array_merge([$name], $constraint));
        }
    }

    public function getConstraints($name) {
        return array_key_exists($name, $this->constraints)?$this->constraints[$name]:[];
    }

    public function isValid($data) {
        $this->errors = [];
        foreach($this->constraints as $name => $constraints) {
            $value = $this->getProperty($data, $name);
            foreach($constraints as $constraint) {
                list($rule, $params, $error) = $constraint;
                switch($rule) {
                case 'email':
                    if (filter_var($value, FILTER_VALIDATE_EMAIL) === false && !is_null($value)) {
                        $this->errors[$name][] = $error;
                    }
                    break;
                case 'inArray':
                    if (!in_array($value, $params[0])) {
                        $this->errors[$name][] = $error;
                    }
                    break;
                case 'numMin':
                    if ($value < $params[0]) {
                        $this->errors[$name][] = $error;
                    }
                    break;
                case 'numMax':
                    if ($value > $params[0]) {
                        $this->errors[$name][] = $error;
                    }
                    break;
                case 'minLength':
                    if (strlen($value) < $params[0]) {
                        $this->errors[$name][] = $error;
                    }
                    break;
                case 'required':
                    if (is_null($value)) {
                        $this->errors[$name][] = $error;
                    }
                    break;
                case 'closure':
                    if (!$params[0]($value, $data)) {
                        $this->errors[$name][] = $error;
                    }
                    break;
                default:
                    throw new \Exception($rule.' validation is not defined');
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors() {
        return $this->errors;
    }
}
