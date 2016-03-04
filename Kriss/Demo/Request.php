<?php

namespace Kriss\Demo;

use Kriss\Mvvm\Request\RequestInterface;

class Request implements RequestInterface {
    public function getHost() {
        return $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
    }

    public function getMethod() {
        $method = $_SERVER['REQUEST_METHOD'];
        if (isset($_GET['_method']) && $method === 'POST') $method = $_GET['_method'];
        return $method;
    }

    public function getRequest() {
        return $_POST;
    }

    public function getQuery() {
        return $_GET;
    }


    public function getUri() {
        $uri = substr($_SERVER['REQUEST_URI'], strlen($_SERVER['SCRIPT_NAME']));
        $pos = strpos($uri, '?');
        if ($pos !== false) $uri = substr($uri, 0, $pos);

        return rtrim($uri,'/').'/';
    }
}
