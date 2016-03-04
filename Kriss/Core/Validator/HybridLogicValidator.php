<?php

namespace Kriss\Core\Validator;

use Kriss\Mvvm\Validator\ValidatorInterface;

class HybridLogicValidator implements ValidatorInterface {
    private $validator;
    private $errors = [];

    public function __construct(\HybridLogic\Validation\Validator $validator) {
        $this->validator = $validator;
    }

    public function setConstraints($constraints)
    {
        foreach($constraints as $constraint) {
            call_user_func_array(array($this, 'addConstraint'), $constraint);
        }
    }
 
    public function isValid($data) {
        return $this->validator->is_valid((array)$data);
    }

    public function getErrors() {
        $errors = $this->validator->get_errors();
        foreach($errors as $key => $error) {
            $errors[$key] = [isset($this->errors[$error])?$this->errors[$error]:$error];
        }
        return $errors;
    }

    private function addConstraint($name, $rule, $params, $error = null)
    {
        $rule = '\\HybridLogic\\Validation\\Rule\\'.ucfirst($rule);
        $reflector = new \ReflectionClass($rule);
        $rule = $reflector->newInstanceArgs($params);
        if (!is_null($error)) {
            $this->errors[$rule->get_error_message($name, '', $this->validator)] = $error;
        }
        $this->validator->add_rule($name, $rule);
    }
}
