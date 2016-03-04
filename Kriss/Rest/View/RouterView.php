<?php

namespace Kriss\Rest\View;

use Kriss\Mvvm\View\ViewInterface;
use Kriss\Mvvm\ViewModel\ListViewModelInterface;
use Kriss\Mvvm\ViewModel\ViewModelInterface as ViewModel;
use Kriss\Mvvm\Router\RouterInterface as Router;

class RouterView implements ViewInterface {
    protected $viewModel;
    protected $router;
    
    public function __construct(ViewModel $viewModel, Router $router) {
        $this->viewModel = $viewModel;
        $this->router = $router;
    }
    
    public function render() {
        $host = 'http'.(!empty($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
        $result = '';

        $string = [];
        $data = $this->viewModel->getData();
        $data = reset($data);
        if (!is_null($data)) {
            foreach($data as $item) {
                $string[] = $item;
            }
        }

        $result .= join(' ', $string);
        if ($this->viewModel instanceOf ListViewModelInterface) {
            $result .= ' <a href="'.$host.$this->router->generate('autoroute_index', $this->router->getParameters()).'">index</a>';
        }
        $result .= ' <a href="'.$host.$this->router->generate('autoroute_new', $this->router->getParameters()).'">new</a>';
        $routerParams = $this->router->getParameters();
        $result .= ' <a href="'.$host.$this->router->generate('autoroute_edit'.(isset($routerParams['id'])?'_id':''), $routerParams).'">edit</a>';
        $result .= ' <a href="'.$host.$this->router->generate('autoroute_delete'.(isset($routerParams['id'])?'_id':''), $routerParams).'">delete</a>';
        $result .= ' <a href="'.$host.'">home</a>';

        return [[], $result];
    }
}
