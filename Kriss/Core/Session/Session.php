<?php

namespace Kriss\Core\Session;

use Kriss\Mvvm\Session\SessionInterface;

class Session implements SessionInterface {
    public function __construct($sessionName = '') {
        if (!isset($_SESSION)) $_SESSION = []; // cli php
        if (!empty($sessionName)) session_name($sessionName);
        
    }

    public function start() {
        if (!$this->isActive()) {
            // Prevent php to use sessionID in URL if cookies are disabled.
            ini_set('session.use_trans_sid', false);
            session_start();
        }
    }

    public function get($key, $default = null) {
        $this->autostart();
        if (array_key_exists($key, $_SESSION)) {
            $default = $_SESSION[$key];
        }
        return $default;
    }

    public function set($key, $value) {
        $this->autostart();
        $_SESSION[$key] = $value;
    }

    public function remove($keys) {
        if (is_string($keys)) {
            $keys = [$keys];
        }
        foreach ($keys as $key) {
            unset($_SESSION[$key]);
        }
    }

    public function isActive() {
        return \PHP_SESSION_ACTIVE === session_status();
    }

    private function autostart() {
        if (!$this->isActive()) {
            $this->start();
        }
    }
}
