<?php

namespace Kriss\Mvvm\Request;

interface RequestInterface {
    public function getHost();
    public function getMethod();
    public function getRequest();
    public function getQuery();
    public function getUri();
    public function getBaseUrl();
    public function getSchemeAndHttpHost();
}
