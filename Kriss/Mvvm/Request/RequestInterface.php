<?php

namespace Kriss\Mvvm\Request;

interface RequestInterface {
    public function getBaseUrl();
    public function getHost();
    public function getMethod();
    public function getPathInfo();
    public function getQuery($query = null, $default = null);
    public function getRequest($request = null, $default = null);
    public function getSchemeAndHttpHost();
    public function getServer($server = null, $default = null);
    public function getUri();
}
