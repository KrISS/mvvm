<?php

namespace Kriss\Rest\View;

use Kriss\Mvvm\View\ViewInterface as ViewInterface;
use Kriss\Mvvm\ViewModel\ViewModelInterface as ViewModel;

class TransphpormView implements ViewInterface {
	protected $viewModel;
    protected $template;
	
	public function __construct(ViewModel $viewModel, \Transphporm\Builder $template) {
		$this->viewModel = $viewModel;
        $this->template = $template;
	}
	
    public function render() {
        $data = $this->viewModel->getData();
        $output = $this->template->output($data);
        return [$output->headers,'<!DOCTYPE html>'.$output->body];
	}
}
