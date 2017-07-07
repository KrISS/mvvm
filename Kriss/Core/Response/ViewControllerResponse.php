<?php

namespace Kriss\Core\Response;

use Kriss\Mvvm\Response\ResponseInterface;

use Kriss\Mvvm\View\ViewInterface;
use Kriss\Mvvm\Controller\ControllerInterface;

class ViewControllerResponse implements ResponseInterface {
    use ResponseTrait;
    
    private $view;
    private $controller;

    public function __construct(ViewInterface $view, ControllerInterface $controller = null) {
        $this->view = $view;
        $this->controller = $controller;
    }

    public function send() {
        if (!is_null($this->controller)) {
            call_user_func(array($this->controller, 'action'));
        }

        list($headers, $body) = $this->view->render();
        $this->sendHeadersBody($headers, $body);
    }
}
