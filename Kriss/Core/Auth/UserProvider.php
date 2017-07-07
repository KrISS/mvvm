<?php

namespace Kriss\Core\Auth;

use Kriss\Mvvm\Auth\UserProviderInterface;

class UserProvider implements UserProviderInterface {
    private $userModel;

    public function __construct($userModel) {$this->userModel = $userModel;}

    public function loadUser($criteria) {return $this->userModel->findOneBy($criteria);}
}
