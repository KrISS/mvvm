<?php

namespace Kriss\Rest\View;

use Kriss\Mvvm\View\ViewInterface;
use Kriss\Mvvm\ViewModel\FormListViewModelInterface;
use Kriss\Mvvm\ViewModel\FormViewModelInterface as FormViewModel;
use Kriss\Mvvm\Router\RouterInterface as Router;

class RouterFormView implements ViewInterface {
    protected $viewModel;
    protected $router;

    public function __construct(FormViewModel $viewModel, Router $router) {
        $this->viewModel = $viewModel;
        $this->router = $router;
    }
    
    public function render() {
        $host = 'http'.(!empty($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
        $result = '';
        
        $data = $this->viewModel->getFormData();
        $errors = $this->viewModel->getErrors();
        $action = $this->viewModel->getAction();

        $result .= '<ul id="errors">';
        foreach($errors as $name => $nameErrors) {
            $result .= '<li>'. $name;
            $result .= '<ul>';
            foreach($nameErrors as $error) {
                $result .= '<li>'. $error .'</li>';
            }
            $result .= '</ul>';
            $result .= '</li>';
        }
        $result .= '</ul>';

        $url = '';
        $routerParams = $this->router->getParameters();
        if ($this->viewModel instanceOf FormListViewModelInterface) {
            $url = $this->router->generate('autoroute_index_id', $routerParams);
        } else {
            $url = $this->router->generate('autoroute_index', $routerParams);
        }
        if (!is_null($data)) {
            foreach($data as $slug => $object) {
                if (!is_null($object)) {
                    $result .= '<form action="'.$host.$url.($action !== 'POST'?'?_method='.$action:'').'" id="'.$slug.'" method="POST">';
                    if ($action !== 'DELETE') {
                        foreach($object as $name => $value) {
                            if ($name != 'id') {
                                $result .= '<div><label>'.$name.': <input name="'.$name.'" value="'.$value.'" /></div>';
                            }
                        }
                    }
                    $result .= '<input type="submit" value="'.$action.'"/>';
                    $result .= '</form>';
                }
            }
        }
        $result .= ' <a href="'.$host.'/'.$slug.'/">index</a>';
        
        return [[], $result];
    }
}
