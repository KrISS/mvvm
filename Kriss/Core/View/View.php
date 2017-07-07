<?php

namespace Kriss\Core\View;

use Kriss\Mvvm\View\ViewInterface;

class View implements ViewInterface {
    use ViewTrait;

    private function classToAttr($class) {return strtolower($class);}
    
    private function stringify($data, $first = false) {
        $string = ['<ul>'];
        foreach($data as $key => $item) {
            $attr = is_object($item)?$this->classToAttr(get_class($item)):(!is_numeric($key)?$key:'');
            $attr = empty($attr)?'':($first?' id="'.$attr.'"':' class="'.$attr.'"');
            if (is_object($item) || is_array($item)) {
                $string[] = '<li'.$attr.'>'.$key.': '.$this->stringify($item).'</li>';
            } else {
                $string[] = '<li'.$attr.'>'.$key.': '.$item.'</li>';
            }
        }
        $string[] = '</ul>';
        return join('', $string);
    }
    
    public function render() {return [[], $this->stringify($this->viewModel->getData(), true)];}
}
