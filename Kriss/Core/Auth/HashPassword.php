<?php

namespace Kriss\Core\Auth;

use Kriss\Mvvm\Auth\HashPasswordInterface;

class HashPassword implements HashPasswordInterface {
    private $salt;

    public function __construct($salt = 'a18c1239f19135e3072d931c1050603f4f405194') {
        $this->$salt = $salt;
    }
    
    public function hash($password, $username = '') {
        return sha1($password.$username.$this->salt);
    }
}
