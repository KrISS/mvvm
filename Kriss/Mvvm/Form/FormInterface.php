<?php

namespace Kriss\Mvvm\Form;

interface FormInterface {
    public function getAction();
    public function getData();
    public function getMethod();
    public function setAction($action);  
    public function setData($data);  
    public function setFormatter($name, $fun);
    public function setFormData($formData);
    public function setMethod($method);
    public function setRule($name, $rule);
    public function getRule($name);
    public function getForm();
}
