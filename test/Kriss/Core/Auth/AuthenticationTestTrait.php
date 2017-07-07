<?php

use Kriss\Mvvm\Auth\AuthenticationInterface;

class User {
    public $username = 'user';
    public $password = 'hash';
}
trait AuthenticationTestTrait {
    public function testAuthenticateUserObject() {
        $user = new User;
        $authentication = $this->getAuthentication($user);
        
        $this->assertSame(false, $authentication->isAuthenticated());
        $this->assertSame(AuthenticationInterface::wrongCredentials, $authentication->authenticate());
        $this->assertSame(false, $authentication->isAuthenticated());
        $this->assertSame(null, $authentication->getUser());

        $this->login();
        $this->assertSame(AuthenticationInterface::authenticationSuccess, $authentication->authenticate());
        $this->assertSame(true, $authentication->isAuthenticated());
        $this->assertSame($user, $authentication->getUser());
        $authentication->deauthenticate();
        $this->assertSame(false, $authentication->isAuthenticated());
        $this->assertSame(null, $authentication->getUser());
    }
    
    public function testAuthenticateUserArray() {
        $user = ['username' => 'user', 'password' => 'hash'];
        $authentication = $this->getAuthentication($user);
        
        $this->assertSame(false, $authentication->isAuthenticated());
        $this->assertSame(AuthenticationInterface::wrongCredentials, $authentication->authenticate());
        $this->assertSame(false, $authentication->isAuthenticated());
        $this->assertSame(null, $authentication->getUser());

        $this->login();
        $this->assertSame(AuthenticationInterface::authenticationSuccess, $authentication->authenticate());
        $this->assertSame(true, $authentication->isAuthenticated());
        $this->assertSame($user, $authentication->getUser());
        $authentication->deauthenticate();
        $this->assertSame(false, $authentication->isAuthenticated());
        $this->assertSame(null, $authentication->getUser());
    }
    
    public function testAuthenticateUnknownUser() {
        $user = ['username' => '', 'password' => ''];
        $authentication = $this->getAuthentication($user);
        
        $this->assertSame(false, $authentication->isAuthenticated());
        $this->assertSame(AuthenticationInterface::wrongCredentials, $authentication->authenticate());
        $this->assertSame(false, $authentication->isAuthenticated());

        $this->login();
        $this->assertSame(AuthenticationInterface::unknownUser, $authentication->authenticate());
        $this->assertSame(false, $authentication->isAuthenticated());
    }
    
    public function testAuthenticateWrongPassword() {
        $user = ['username' => 'user', 'password' => ''];
        $authentication = $this->getAuthentication($user);
        
        $this->assertSame(false, $authentication->isAuthenticated());
        $this->assertSame(AuthenticationInterface::wrongCredentials, $authentication->authenticate());
        $this->assertSame(false, $authentication->isAuthenticated());

        $this->login();
        $this->assertSame(AuthenticationInterface::wrongPassword, $authentication->authenticate());
        $this->assertSame(false, $authentication->isAuthenticated());
    }
    
    public function testAutoAuthenticate() {
        $user = ['username' => 'user', 'password' => 'hash'];
        $authentication = $this->getAuthentication($user);
        
        $this->autoLogin();
        $this->assertSame(AuthenticationInterface::alreadyAuthenticated, $authentication->authenticate());
        $this->assertSame(true, $authentication->isAuthenticated());
        $this->assertSame($user, $authentication->getUser());
    }
}
