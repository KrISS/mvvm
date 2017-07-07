<?php

namespace Kriss\Mvvm\Auth;

interface UserProviderInterface { 
    public function loadUser($criteria);
}
