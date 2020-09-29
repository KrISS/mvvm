<?php

namespace Kriss\Core\View;

use Kriss\Mvvm\View\ViewInterface;
use Kriss\Mvvm\ViewModel\FormViewModelInterface;

class FormView implements ViewInterface {
    protected $viewModel;

    public function __construct(FormViewModelInterface $viewModel) {$this->viewModel = $viewModel;}
    
    private function stringify($data) {
        $string = ['<ul>'];
        foreach($data as $key => $item) {
            $attr = '';
            if (is_object($item) || is_array($item)) {
                $string[] = '<li'.$attr.'>'.$key.': '.$this->stringify($item).'</li>';
            } else {
                $string[] = '<li'.$attr.'>'.$key.': '.$item.'</li>';
            }
        }
        $string[] = '</ul>';
        return join('', $string);
    }
    
    public function render() {
        $result = '';

        $data = $this->viewModel->getData();
        $errors = $this->viewModel->getErrors();
        
        $result .= $this->stringify($errors);

        if (!is_null($data)) {
            foreach($data as $slug => $object) {
                if (!is_null($object)) {
                    $method = $object['*']['method'];
                    $url = $object['*']['action'];
                    $result .= '<form action="'.$url.'" id="'.$slug.'" method="'.$method.'">';
                    if (isset($object['_method']['value'])) $method = $object['_method']['value'];
                    if ($method != 'DELETE') {
                        foreach($object as $name => $value) {
                            if ($name != 'id' && $name[0] != '*') {
                                switch($value['type']) {
                                case 'textarea':
                                    $result .= '<div><label>'.$name.': <textarea name="'.$name.'">'.$value['value'].'</textarea></label></div>';
                                    break;
                                default:
                                    $result .= '<div><label>'.$name.': <input name="'.$name.'" value="'.$value['value'].'" type="'.$value['type'].'"/></label></div>';
                                }
                            }
                        }
                    } else {
                        foreach($object as $name => $value) {
                            if ($name != 'id' && $name[0] != '*') {
                                if ($name === '_method') {
                                    $result .= '<input name="'.$name.'" value="'.$value['value'].'" type="'.$value['type'].'"/>';
                                } else {
                                    $result .= '<div>'.$name.': '.$value['value'].'</div>';
                                }
                            }
                        }
                    }
                    $result .= '<input type="submit" value="'.$method.'"/>';
                    $result .= '</form>';
                }
            }
        }
        return [[], $result];
    }
}
