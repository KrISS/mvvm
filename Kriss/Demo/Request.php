<?php

namespace Kriss\Demo;

use Kriss\Mvvm\Request\RequestInterface;

class Request implements RequestInterface {
    public function getQuery($query = null, $default = null) { return $_GET; }
    public function getBaseUrl() {}
    public function getHost() { }
    public function getMethod() { }
    public function getPathInfo() {}
    public function getRequest($request = null, $default = null) { }
    public function getServer($server = null, $default = null) { }
    public function getSchemeAndHttpHost() {}
    public function getUri() { }
}
