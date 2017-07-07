<?php

namespace Kriss\Core\Auth;

use Kriss\Mvvm\Auth\AuthenticationInterface;

use Kriss\Mvvm\Auth\HashPasswordInterface;
use Kriss\Mvvm\Auth\UserProviderInterface;
use Kriss\Mvvm\Request\RequestInterface;
use Kriss\Mvvm\Session\SessionInterface;

class BasicAuthentication implements AuthenticationInterface {
    private $passwordHash;
    private $request;
    private $session;
    private $user;
    private $userProvider;

    public function __construct(UserProviderInterface $userProvider, RequestInterface $request, SessionInterface $session, HashPasswordInterface $passwordHash) {
        $this->passwordHash = $passwordHash;
        $this->request = $request;
        $this->session = $session;
        $this->userProvider = $userProvider;
        $this->user = null;
    }
  
    public function authenticate() {
        if ($this->isAuthenticated()) {
            $this->user = $this->userProvider->loadUser(['username' => $this->session->get('username')]);
            return AuthenticationInterface::alreadyAuthenticated;
        }
        $secretCaptcha = $this->session->get('secret_captcha', (new \DateTime())->format('is'));
        $secret = $this->session->get('secret', $secretCaptcha);

        $username = $this->request->getServer('PHP_AUTH_USER');
        $password = $this->request->getServer('PHP_AUTH_PW');
        if (is_null($username) || is_null($password)) {
            return AuthenticationInterface::wrongCredentials;
        }

        $user = $this->userProvider->loadUser(['username' => $username]);
        if (is_null($user)) {
            return AuthenticationInterface::unknownUser;
        }

        $userPassword = null;
        if (is_array($user)) { $userPassword = $user['password']; }
        else { $userPassword = $user->password; }
        if ($this->passwordHash->hash($password, $username) !== $userPassword) {
            return AuthenticationInterface::wrongPassword;
        }

        // trick to enable logout => need to login twice
        if ($secret !== $secretCaptcha) {
            $this->session->remove('secret');
            return AuthenticationInterface::wrongCredentials;
        }
        $this->user = $user;

        $this->session->set('uid', sha1(uniqid('', true).'_'.mt_rand()));
        $this->session->set('username', $username);
        $this->session->set('secret', $secret);

        return AuthenticationInterface::authenticationSuccess;
    }

    public function deauthenticate() {
        $this->user = null;
        $this->session->remove(['secret_captcha', 'uid']);
    }

    public function getUser() {
        return $this->user;
    }

    public function isAuthenticated() {
        return ((bool)$this->session->get('uid', false)) && ((bool)$this->session->get('username', false));
    }
}
