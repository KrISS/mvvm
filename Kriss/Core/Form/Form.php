<?php

namespace Kriss\Core\Form;

use Kriss\Mvvm\Form\FormInterface;

class Form implements FormInterface {
    protected $rules;
    protected $formatters;
    protected $data;
    protected $formData;

    public function __construct($data = [], $method = 'GET', $action = '') {
        $this->rules = [];
        $this->setAction($action);
        $this->setMethod($method);
        $this->data = $data;
        $this->formData = [];
        $this->formatters = [];
    }

    private function updateCurrentData(&$data, $key, $value) {
        if (is_array($data)) {$data[$key] = $value;}
        else {$data->$key = $value;}
    }
    
    private function updateData(&$data, $formData) {
        foreach ($data as $key => $value) {
            if (array_key_exists($key, $formData) && $key !== 'id') {
                if (is_object($value) || is_array($value)) {
                    if (is_array($data)) {
                        $this->updateData($data[$key], $formData[$key]);
                    } else {
                        $this->updateData($data->$key, $formData[$key]);
                    }
                } else {
                    $value = $this->formatValue($key, $formData[$key]);
                    $this->updateCurrentData($data, $key, $value);
                }
            }
        }
    }
    
    public function getData($name = '') {
        $this->updateData($this->data, (empty($name)?$this->formData:$this->formData[$name]));
        return $this->data;
    }
   
    public function setData($data) {$this->data = $data;}

    public function setFormatter($name, $fun) {$this->formatters[$name] = $fun;}

    private function setCurrentFormData(&$formData, $key, $value) {
        $pos = strpos($key, '[');
        if ($pos === false) {
            if (empty($key) || $key === '_') {$formData = $value;}
            else $formData[$key] = $value;
        } else {
            $currentKey = substr($key,0,$pos);
            if (!array_key_exists($currentKey, $formData)) {
                $formData[$currentKey] = [];
            }
            $key = substr($key,$pos+1);
            $pos = strpos($key, ']');
            if ($pos !== false) {
                $key = substr_replace($key, '', $pos, 1);
            }
            if ($currentKey !== '_') {
                $formData[$currentKey] = $this->setCurrentFormData($formData[$currentKey], $key, $value);
            } else {
                $formData = $this->setCurrentFormData($formData[$currentKey], $key, $value);
            }
                
        }
        return $formData;
    }
    
    public function setFormData($formData) {
        $this->formData = [];
        foreach ($formData as $key => $value) {
            $this->setCurrentFormData($this->formData, $key, $value);
        }
    }

    public function getAction() {return $this->rules['*']['action'];}

    public function setAction($action) {$this->rules['*']['action'] = $action;}

    public function getMethod() {
        if (isset($this->rules['_method']))
            return $this->rules['_method']['value'];
        return $this->rules['*']['method'];
    }
    
    public function setMethod($method) {
        switch($method) {
        case 'GET':
            $this->rules['*']['method'] = $method;
            break;
        case 'POST':
            $this->rules['*']['method'] = $method;
            break;
        default:
            $this->rules['*']['method'] = 'POST';
            $this->rules['_method'] = [
                'type' => 'hidden',
                'value' => $method,
            ];
        }
    }
    
    public function setRule($name, $rule) {$this->rules[$name] = $rule;}

    public function getRule($name) {return array_key_exists($name, $this->rules)?$this->rules[$name]:[];}

    public function generateRules($data, $name) {
        foreach($data as $key => $value) {
            if (is_numeric($key)) {
                $this->generateRules($value, (empty($name)?'_':$name).'['.$key.']');
            } else {
                $currentName = (empty($name)?'':$name.'[').$key.(empty($name)?'':']');
                if (!array_key_exists($key, $this->rules)) {
                    $this->rules[$currentName] = [];
                }
                if ($this->rules[$currentName] instanceOf Form) {
                    $form = $this->rules[$currentName]->getForm();
                    foreach ($form as $sub => $rule) {
                        $openingBracket = '[';
                        $closingBracket = ']';
                        if ($sub[0] === '_') {
                            $sub = substr($sub, 1);
                            $openingBracket = '';
                            $closingBracket = '';
                        }
                        if ($sub !== '*') $this->rules[$currentName.$openingBracket.$sub.$closingBracket] = $rule;
                    };
                    unset($this->rules[$currentName]);
                } else {
                    if (!is_null($this->rules[$currentName])) {
                        if (!array_key_exists('type', $this->rules[$currentName])) {
                            $this->rules[$currentName]['type'] = 'text';
                        } 
                        if (!array_key_exists('value', $this->rules[$currentName])) {
                            $this->rules[$currentName]['value'] = $value;
                        }
                    }
                }
            }
        }
    }
    
    public function getForm($name = '') {
        $this->generateRules($this->data, $name);
        return array_filter($this->rules);
    }

    private function formatValue($name, $value) {
        if (array_key_exists($name, $this->formatters)) {
            $value = $this->formatters[$name]($value, $this->formData);
        }

        return $value;
    }
}
