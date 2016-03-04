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

    protected function generateModel($className)
    {
        parent::generateModel($className);
        $this->rules[$className.'\\Model']['instanceOf'] = 'Kriss\\Core\\Model\\ArrayModel';
    } 

    protected function autoIndex($className)
    {
        $this->generateModel($className);
        $this->generateListViewModel($className);
        $this->generateListView($className);
    }

    protected function autoIndexId($className, $id)
    { 
        $this->generateModel($className);
        $this->generateListViewModel($className);
        $this->generateView($className);
        $this->generateListSelectController($className, $id);
        $this->rules[$className.'\\Controller'] = $this->rules[$className.'\\ListSelectController'];
    }

    protected function autoEditId($className, $id)
    {
        $this->generateModel($className);
        $this->generateFormListViewModel($className, null, ['instance' => $className], 'PUT');
        $this->generateFormView($className);
        $this->generateFormListSelectController($className, $id);
        $this->rules[$className.'\\Controller'] = $this->rules[$className.'\\FormListSelectController'];
    }

    protected function autoUpdateId($className, $id)
    {
        $this->autoEditId($className, $id);
        $this->generateValidator($className);
        $this->rules[$className.'\\ViewModel']['constructParams'][1] = ['instance' => $className.'\\Validator'];
        $this->generateFormController($className);
        $this->generateListController($className, ['FormListSelectController', 'FormController']);
    }

    protected function autoDeleteId($className, $id)
    {
        $this->autoEditId($className, $id);
        $this->rules[$className.'\\ViewModel']['constructParams'][3] = 'DELETE';
    }
    
    protected function autoRemoveId($className, $id)
    {
        $this->autoDeleteId($className, $id);
        $this->rules[$className.'\\ViewModel']['instanceOf'] = 'Kriss\\Core\\ViewModel\\DeleteFormListViewModel';
        $this->rules[$className.'\\ViewModel']['constructParams'][1] = null;
        $this->generateFormController($className);
        $this->generateListController($className, ['FormListSelectController', 'FormController']);
    }

    private function generateFormListSelectController($className, $id)
    {
        $this->rules[$className.'\\FormListSelectController'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\FormListSelectController',
            'constructParams' => [
                ['instance' => $className.'\\ViewModel'],
                $id,
            ]
        ];
    }

    private function generateFormListViewModel($className, $validator = null, $data = null, $method = 'POST')
    {
        $this->generateFormViewModel($className, $validator, $data, $method);
        $this->rules[$className.'\\ViewModel']['instanceOf'] = 'Kriss\\Core\\ViewModel\\FormListViewModel';
    }

    private function generateListSelectController($className, $id)
    {
        $this->rules[$className.'\\ListSelectController'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\ListSelectController',
            'constructParams' => [
                ['instance' => $className.'\\ViewModel'],
                $id,
            ]
        ];
    }
    
    private function generateListViewModel($className)
    {
        $this->rules[$className.'\\ViewModel'] = [
            'instanceOf' => 'Kriss\\Core\\ViewModel\\ListViewModel',
            'shared' => true,
            'constructParams' => [
                ['instance' => $className.'\\Model'],
            ]
        ];
    }

    private function generateListView($className)
    {
        $this->rules[$className.'\\View'] = [
            'instanceOf' => 'Kriss\\Rest\\View\\RouterListView',
            'constructParams' => [
                ['instance' => $className.'\\ViewModel'],
                ['instance' => 'Router'],
            ]
        ];
    }
}
