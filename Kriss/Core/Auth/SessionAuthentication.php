<?php

namespace Kriss\Core\Auth;

use Kriss\Mvvm\Auth\AuthenticationInterface;

use Kriss\Mvvm\Auth\UserProviderInterface;
use Kriss\Mvvm\Auth\HashPasswordInterface;
use Kriss\Mvvm\Request\RequestInterface;
use Kriss\Mvvm\Session\SessionInterface;

class SessionAuthentication implements AuthenticationInterface {
    private $passwordHash;
    private $request;
    private $session;
    private $user = null;
    private $userProvider;

    // options
    private $inactivityTimeout = 3600;
    private $sessionName = '';
    private $disableSessionProtection = false;

    public function __construct(UserProviderInterface $userProvider, RequestInterface $request, SessionInterface $session, HashPasswordInterface $passwordHash, $options = []) {
        $this->passwordHash = $passwordHash;
        $this->request = $request;
        $this->session = $session;
        $this->userProvider = $userProvider;
        
        $this->loadOptions($options);

        // Force cookie path (but do not change lifetime)
        $cookie = session_get_cookie_params();
        // Default cookie expiration and path.
        $cookiedir = '';
        if (dirname($this->request->getServer('SCRIPT_NAME', ''))!='/') {
            $cookiedir = dirname($this->request->getServer('SCRIPT_NAME', '')).'/';
        }
        $ssl = true;
        if ($this->request->getServer('HTTPS','') !== 'on') {
            $ssl = false;
        }
        session_set_cookie_params($cookie['lifetime'], $cookiedir, $this->request->getServer('HTTP_HOST'), $ssl);
        // Use cookies to store session.
        ini_set('session.use_cookies', 1);
        // Force cookies for session  (phpsessionID forbidden in URL)
        ini_set('session.use_only_cookies', 1);
        if (!session_id()) {
            // Prevent php to use sessionID in URL if cookies are disabled.
            ini_set('session.use_trans_sid', false);
            if (!empty($this->sessionName)) {
                session_name($this->sessionName);
            }
            session_start();
        }
    }

    public function authenticate() {
        if ($this->isAuthenticated()) {
            $this->user = $this->userProvider->loadUser(['username' => $this->session->get('username')]);
            return AuthenticationInterface::alreadyAuthenticated;
        }
        
        $request = $this->request->getRequest();
        if (!array_key_exists('username', $request) || !array_key_exists('password', $request)) {
            return AuthenticationInterface::wrongCredentials;
        }

        $username = $request['username'];
        $password = $request['password'];

        $user = $this->userProvider->loadUser(['username' => $username]);
        if (is_null($user)) {
            return AuthenticationInterface::unknownUser;
        }

        $userPassword = null;
        if (is_array($user)) { $userPassword = $user['password']; }
        else { $userPassword = $user->password; }
        if ($userPassword !== $this->passwordHash->hash($password, $username)) {
            return AuthenticationInterface::wrongPassword;
        }

        $this->user = $user;
        
        // Generate unique random number to sign forms (HMAC)
        $this->session->set('uid', sha1(uniqid('', true).'_'.mt_rand()));
        $this->session->set('ip', $this->allIPs());
        $this->session->set('username', $username);
        // Set session expiration.
        $this->session->set('expires_on', time() + $this->inactivityTimeout);

        return AuthenticationInterface::authenticationSuccess;
    }

    public function deauthenticate() {
        $this->user = null;
        $this->session->remove(['uid', 'ip', 'expires_on']);
    }

    public function getUser() {return $this->user;}

    public function isAuthenticated() {
        if (!(bool)$this->session->get('uid', false)
            || ($this->disableSessionProtection === false
                && $this->session->get('ip') !== $this->allIPs())
            || time() >= $this->session->get('expires_on')) {
            $this->deauthenticate();

            return false;
        }
        // User accessed a page : Update his/her session expiration date.
        $this->session->set('expires_on', time() + $this->inactivityTimeout);
        if (!empty($this->session->get('longlastingsession'))) {
            $this->session->set('expires_on', $this->session->get('expires_on') + $this->session->get('longlastingsession'));
        }

        return true;
    }

    private function allIPs() {
        return $this->request->getServer('REMOTE_ADDR','')
            .'_'.$this->request->getServer('HTTP_X_FORWARDED_FOR', '')
            .'_'.$this->request->getServer('HTTP_CLIENT_IP', '');
    }

    private function loadOptions($options) {
        foreach($options as $key => $option) {
            if (in_array($key, ['disableSessionProtection', 'inactivityTimeout', 'sessionName'])) {
                $this->$key = $option;
            }
        }
    }
}
