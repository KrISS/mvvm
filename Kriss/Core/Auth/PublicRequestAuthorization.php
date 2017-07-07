<?php

namespace Kriss\Core\Auth;

use Kriss\Mvvm\Auth\AuthorizationInterface;

use Kriss\Mvvm\Auth\AuthenticationInterface;
use Kriss\Mvvm\Request\RequestInterface;

class PublicRequestAuthorization implements AuthorizationInterface {
    private $authentication;
    private $request;

    public function __construct(AuthenticationInterface $authentication, RequestInterface $request) {
        $this->authentication = $authentication;
        $this->request = $request;
    }

    public function isGranted() {return true;}
}
