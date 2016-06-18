<?php

namespace Kriss\Rest\Router;

use \Kriss\Mvvm\Container\ContainerInterface;
use \Kriss\Mvvm\Router\RouterInterface;
use \Kriss\Mvvm\Model\ListModelInterface;

class AutoListRoute extends AutoRoute {
    public function addResponses()
    {
        parent::addResponses();
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/<id:\d+>/', 'index_id');
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/<id:\d+>/edit/', 'edit_id');
        $this->addResponse($this->router, 'PUT', '/'.$this->prefix.'/<id:\d+>/', 'update_id');
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/<id:\d+>/delete/', 'delete_id');
        $this->addResponse($this->router, 'DELETE', '/'.$this->prefix.'/<id:\d+>/', 'remove_id');
    }

    protected function generateModel($slug, $action)
    {
        parent::generateModel($slug, $action);
        $this->rules[$slug][$action]['model']['instanceOf'] = 'Kriss\\Core\\Model\\ArrayModel';
    } 

    protected function autoIndex($slug, $action)
    {
        $this->generateModel($slug, $action);
        $this->generateListViewModel($slug, $action);
        $this->generateListView($slug, $action);
    }

    protected function autoIndexId($slug, $action, $id)
    { 
        $this->generateModel($slug, $action);
        $this->generateListViewModel($slug, $action);
        $this->generateView($slug, $action);
        $this->generateListSelectController($slug, $action, $id);
        $this->rules[$slug][$action]['controller'] = $this->rules[$slug][$action]['list_select_controller'];
    }

    protected function autoEditId($slug, $action, $id)
    {
        $this->generateModel($slug, $action);
        $this->generateFormListViewModel($slug, $action, null, ['instance' => $this->getClass($slug)], 'PUT');
        $this->generateFormView($slug, $action);
        $this->generateFormListSelectController($slug, $action, $id);
        $this->rules[$slug][$action]['controller'] = $this->rules[$slug][$action]['form_list_select_controller'];
    }

    protected function autoUpdateId($slug, $action, $id)
    {
        $this->autoEditId($slug, $action, $id);
        $this->generateValidator($slug, $action);
        $this->rules[$slug][$action]['view_model']['constructParams'][1] = ['instance' => '$'.$slug.'_'.$action.'_validator'];
        $this->generateFormController($slug, $action);
        $this->generateListController($slug, $action, [
            '$'.$slug.'_'.$action.'_form_list_select_controller',
            '$'.$slug.'_'.$action.'_form_controller'
        ]);
    }

    protected function autoDeleteId($slug, $action, $id)
    {
        $this->autoEditId($slug, $action, $id);
        $this->rules[$slug][$action]['view_model']['constructParams'][3] = 'DELETE';
    }
    
    protected function autoRemoveId($slug, $action, $id)
    {
        $this->autoDeleteId($slug, $action, $id);
        $this->rules[$slug][$action]['view_model']['instanceOf'] = 'Kriss\\Core\\ViewModel\\DeleteFormListViewModel';
        $this->rules[$slug][$action]['view_model']['constructParams'][1] = null;
        $this->generateFormController($slug, $action);
        $this->generateListController($slug, $action, [
            '$'.$slug.'_'.$action.'_form_list_select_controller',
            '$'.$slug.'_'.$action.'_form_controller'
        ]);
    }

    private function generateFormListSelectController($slug, $action, $id)
    {
        $this->rules[$slug][$action]['form_list_select_controller'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\FormListSelectController',
            'constructParams' => [
                ['instance' => '$'.$slug.'_'.$action.'_view_model'],
                $id,
            ]
        ];
    }

    private function generateFormListViewModel($slug, $action, $validator = null, $data = null, $method = 'POST')
    {
        $this->generateFormViewModel($slug, $action, $validator, $data, $method);
        $this->rules[$slug][$action]['view_model']['instanceOf'] = 'Kriss\\Core\\ViewModel\\FormListViewModel';
    }

    private function generateListSelectController($slug, $action, $id)
    {
        $this->rules[$slug][$action]['list_select_controller'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\ListSelectController',
            'constructParams' => [
                ['instance' => '$'.$slug.'_'.$action.'_view_model'],
                $id,
            ]
        ];
    }
    
    private function generateListViewModel($slug, $action)
    {
        $this->rules[$slug][$action]['view_model'] = [
            'instanceOf' => 'Kriss\\Core\\ViewModel\\ListViewModel',
            'shared' => true,
            'constructParams' => [
                ['instance' => '$'.$slug.'_'.$action.'_model'],
            ]
        ];
    }

    private function generateListView($slug, $action)
    {
        $this->rules[$slug][$action]['view'] = [
            'instanceOf' => 'Kriss\\Rest\\View\\RouterListView',
            'constructParams' => [
                ['instance' => '$'.$slug.'_'.$action.'_view_model'],
                ['instance' => 'Router'],
            ]
        ];
    }
}
