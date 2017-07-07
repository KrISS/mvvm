<?php

namespace Kriss\Core\Validator;

use Kriss\Mvvm\Validator\ValidatorInterface;

class HybridLogicValidator implements ValidatorInterface {
    private $emptyValidator;
    private $validator;
    private $constraints = [];
    private $errors = [];

    public function __construct(\HybridLogic\Validation\Validator $validator) {
        $this->emptyValidator = $validator;
        $this->validator = $this->getClone();
    }

    private function getClone() {return clone($this->emptyValidator);}
    
    private function setConstraint($name, $rule, $params = [], $error = '') {
        if (empty($error)) $error = $name.' '.$rule.' Error';
        $this->constraints[$name][] = [$rule, $params, $error];
        call_user_func_array(array($this, 'addConstraint'), [$name, $rule, $params, $error]);
    }

    public function setConstraints($name, $constraints = []) {
        $this->validator = $this->getClone();
        // HybridLogicValidator can not remove a rule
        foreach($this->constraints as $current => $currentConstraints) {
            if ($current != $name) {
                foreach($currentConstraints as $constraint) {
                    call_user_func_array(array($this, 'setConstraint'), array_merge([$current], $constraint));
                }
            }
        }
        $this->constraints[$name] = [];
        foreach($constraints as $constraint) {
            call_user_func_array(array($this, 'setConstraint'), array_merge([$name], $constraint));
        }
    }

    public function getConstraints($name) {return array_key_exists($name, $this->constraints)?$this->constraints[$name]:[];}
 
    public function isValid($data) {return $this->validator->is_valid((array)$data);}

    public function getErrors() {
        $errors = $this->validator->get_errors();
        foreach($errors as $key => $error) {
            $errors[$key] = [isset($this->errors[$error])?$this->errors[$error]:$error];
        }
        return $errors;
    }

    private function addConstraint($name, $rule, $params, $error = null) {
        if ($rule === 'required') $rule = 'NotEmpty';
        $rule = '\\HybridLogic\\Validation\\Rule\\'.ucfirst($rule);
        $reflector = new \ReflectionClass($rule);
        $rule = $reflector->newInstanceArgs($params);
        if (!is_null($error)) {
            $this->errors[$rule->get_error_message($name, '', $this->validator)] = $error;
        }
        $this->validator->add_rule($name, $rule);
    }
}
