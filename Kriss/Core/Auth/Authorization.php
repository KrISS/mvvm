<?php

namespace Kriss\Core\Auth;

use Kriss\Mvvm\Auth\AuthorizationInterface;
use Kriss\Mvvm\Auth\AuthenticationInterface;

class Authorization implements AuthorizationInterface {
    private $authentication;

    public function __construct(AuthenticationInterface $authentication) {$this->authentication = $authentication;}

    public function isGranted() {return $this->authentication->isAuthenticated();}
}
