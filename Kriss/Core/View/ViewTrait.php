<?php
  
namespace Kriss\Core\View;

use Kriss\Mvvm\ViewModel\ViewModelInterface;

trait ViewTrait {
    protected $viewModel;
    
    public function __construct(ViewModelInterface $viewModel) {
         $this->viewModel = $viewModel;
    }
}
