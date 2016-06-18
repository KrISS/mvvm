<?php

namespace Kriss\Rest\Router;

use \Kriss\Mvvm\Container\ContainerInterface;
use \Kriss\Mvvm\Router\RouterInterface;

class AutoSingleRoute extends AutoRoute {
    public function addResponses()
    {
        parent::addResponses();
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/edit/', 'edit');
        $this->addResponse($this->router, 'PUT', '/'.$this->prefix.'/', 'update');
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/delete/', 'delete');
        $this->addResponse($this->router, 'DELETE', '/'.$this->prefix.'/', 'remove');
    }

    protected function autoEdit($slug, $action)
    {
        $this->autoNew($slug, $action);
        $this->rules[$slug][$action]['view_model']['constructParams'][3] = 'PUT';
        $this->generateFormSelectController($slug, $action);
        $this->rules[$slug][$action]['controller'] = $this->rules[$slug][$action]['form_select_controller'];
    }

    protected function autoUpdate($slug, $action) {
        $this->autoEdit($slug, $action);
        $this->generateValidator($slug, $action);
        $this->rules[$slug][$action]['view_model']['constructParams'][1] = ['instance' => '$'.$slug.'_'.$action.'_validator'];
        $this->generateFormController($slug, $action);
        $this->generateListController($slug, $action, [
            '$'.$slug.'_'.$action.'_form_select_controller',
            '$'.$slug.'_'.$action.'_form_controller'
        ]);
    }

    protected function autoDelete($slug, $action)
    {
        $this->autoEdit($slug, $action);
        $this->rules[$slug][$action]['view_model']['constructParams'][3] = 'DELETE';
    }

    protected function autoRemove($slug, $action) {
        $this->autoDelete($slug, $action);
        $this->rules[$slug][$action]['view_model']['instanceOf'] = 'Kriss\\Core\\ViewModel\\DeleteFormViewModel';
        $this->rules[$slug][$action]['view_model']['constructParams'][1] = null;
        $this->generateFormController($slug, $action);
        $this->generateListController($slug, $action, ['$'.$slug.'_'.$action.'_form_select_controller', '$'.$slug.'_'.$action.'_form_controller']);
    }

    private function generateFormSelectController($slug, $action)
    {
        $this->rules[$slug][$action]['form_select_controller'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\FormSelectController',
            'constructParams' => [
                ['instance' => '$'.$slug.'_'.$action.'_view_model'],
            ]
        ];
    }
}
