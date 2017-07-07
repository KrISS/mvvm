<?php

namespace Kriss\Mvvm\Auth;

interface HashPasswordInterface { 
    public function hash($password, $username = '');
}
