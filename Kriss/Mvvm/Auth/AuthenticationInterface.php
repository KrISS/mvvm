<?php

namespace Kriss\Mvvm\Auth;

interface AuthenticationInterface { 
    const authenticationSuccess = 1;
    const alreadyAuthenticated = 2;
    const unknownUser = 3;
    const wrongPassword = 4;
    const wrongCredentials = 5;

    public function authenticate();
    public function deauthenticate();
    public function getUser();
    public function isAuthenticated();
}
