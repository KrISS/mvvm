<?php

namespace Kriss\Rest\Router;

use \Kriss\Mvvm\Container\ContainerInterface;
use \Kriss\Mvvm\Router\RouterInterface;
use \Kriss\Mvvm\Model\ListModelInterface;

class AutoSingleRoute extends AutoRoute {
    public function addResponses()
    {
        parent::addResponses();
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/edit/', 'edit');
        $this->addResponse($this->router, 'PUT', '/'.$this->prefix.'/', 'update');
        $this->addResponse($this->router, 'GET', '/'.$this->prefix.'/delete/', 'delete');
        $this->addResponse($this->router, 'DELETE', '/'.$this->prefix.'/', 'remove');
    }

    protected function autoEdit($className)
    {
        $this->autoNew($className);
        $this->rules[$className.'\\ViewModel']['constructParams'][3] = 'PUT';
        $this->generateFormSelectController($className);
        $this->rules[$className.'\\Controller'] = $this->rules[$className.'\\FormSelectController'];
    }

    protected function autoUpdate($className) {
        $this->autoEdit($className);
        $this->generateValidator($className);
        $this->rules[$className.'\\ViewModel']['constructParams'][1] = ['instance' => $className.'\\Validator'];
        $this->generateFormController($className);
        $this->generateListController($className, ['FormSelectController', 'FormController']);
    }

    protected function autoDelete($className)
    {
        $this->autoEdit($className);
        $this->rules[$className.'\\ViewModel']['constructParams'][3] = 'DELETE';
    }

    protected function autoRemove($className) {
        $this->autoDelete($className);
        $this->rules[$className.'\\ViewModel']['instanceOf'] = 'Kriss\\Core\\ViewModel\\DeleteFormViewModel';
        $this->rules[$className.'\\ViewModel']['constructParams'][1] = null;
        $this->generateFormController($className);
        $this->generateListController($className, ['FormSelectController', 'FormController']);
    }

    private function generateFormSelectController($className)
    {
        $this->rules[$className.'\\FormSelectController'] = [
            'instanceOf' => 'Kriss\\Core\\Controller\\FormSelectController',
            'constructParams' => [
                ['instance' => $className.'\\ViewModel'],
            ]
        ];
    }
}
