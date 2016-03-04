<?php

namespace Kriss\Core\View;

use Kriss\Mvvm\View\ViewInterface as ViewInterface;
use Kriss\Mvvm\ViewModel\ViewModelInterface as ViewModel;

class View implements ViewInterface {
    protected $viewModel;
    
    public function __construct(ViewModel $viewModel) {
        $this->viewModel = $viewModel;
    }
    
    public function render() {
        $result = '';
        $string = [];
        foreach(reset($this->viewModel->getData()) as $item) {
            if (is_object($item) || is_array($item)) {
                foreach($item as $subItem) {
                    $string[] = $subItem;
                }
            } else {
                $string[] = $item;
            }
        }
        $result .= join(' ', $string);

        return [[], $result];
    }
}
