<?php

namespace Kriss\Demo;

use Kriss\Mvvm\App\AppInterface;

class App implements AppInterface 
{
    public function addPlugin($name) {}
    public function configPlugin($name, $config) {}
    public function run()
    {
        $request = new Request();
        $model = new Model();
        $formAction = new FormAction($model);
        $validator = new Validator();
        $viewModel = new ViewModel($model, $validator);
        $controller = new Controller($viewModel, $request, $formAction);
        $controller->action();
        $view = new View($viewModel);
        list($headers, $body) = $view->render();
        $response = new Response($body, $headers);

        return $response->send();
    }
}
