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
        $routerParams = $this->router->getParameters();
        $routerParamsWithoutId = $routerParams;
        unset($routerParamsWithoutId['id']);
        if ($this->viewModel instanceOf ListViewModelInterface) {
            $result .= ' <a href="'.$this->router->generate('autoroute_index', $routerParamsWithoutId, true).'">index</a>';
        }
        $result .= ' <a href="'.$this->router->generate('autoroute_new', $routerParamsWithoutId).'">new</a>';
        $result .= ' <a href="'.$this->router->generate('autoroute_edit'.(isset($routerParams['id'])?'_id':''), (isset($routerParams['id'])?$routerParams:$routerParamsWithoutId), true).'">edit</a>';
        $result .= ' <a href="'.$this->router->generate('autoroute_delete'.(isset($routerParams['id'])?'_id':''), (isset($routerParams['id'])?$routerParams:$routerParamsWithoutId), true).'">delete</a>';
        $result .= ' <a href="'.$this->router->generate('autoroute_homepage', [], true).'">home</a>';

        return [[], $result];
    }
}
