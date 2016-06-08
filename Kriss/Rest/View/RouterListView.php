<?php

namespace Kriss\Rest\View;

use Kriss\Mvvm\View\ViewInterface;
use Kriss\Mvvm\ViewModel\ViewModelInterface as ViewModel;
use Kriss\Mvvm\Router\RouterInterface as Router;

class RouterListView implements ViewInterface {
    protected $viewModel;
    protected $router;
    
    public function __construct(ViewModel $viewModel, Router $router) {
        $this->viewModel = $viewModel;
        $this->router = $router;
    }
    
    public function render() {
        $result = '';
        $result .= ' <a href="'.$this->router->generate('autoroute_new', $this->router->getParameters(), true).'">new</a>';
        $result .= ' <a href="'.$this->router->generate('autoroute_homepage', [], true).'">home</a>';

        $string = [];
        $data = $this->viewModel->getData();
        foreach ($data as $slug => $collection) {
            $result .= '<ul id="'.$slug.'">';
            foreach($collection as $id => $object) {
                $string = [];
                foreach($object as $item) {
                    $string[] = $item;
                }
                $result .= '<li class="'.strtolower(get_class($object)).'">'.join(' ', $string);
                $routerParams = $this->router->getParameters();
                $routerParams = array_merge($routerParams, ['id' => $id]);
                $result .= ' <a href="'.$this->router->generate('autoroute_index_id', $routerParams, true).'">show</a>';
                $result .= ' <a href="'.$this->router->generate('autoroute_edit_id', $routerParams, true).'">edit</a>';
                $result .= ' <a href="'.$this->router->generate('autoroute_delete_id', $routerParams, true).'">delete</a>';
                $result .= '</li>';
            }
            $result .= '</ul>';
        }

        $result .= ' <a href="'.$this->router->generate('autoroute_new', $this->router->getParameters(), true).'">new</a>';
        $result .= ' <a href="'.$this->router->generate('autoroute_homepage', [], true).'">home</a>';

        return [[], $result];
    }
}
