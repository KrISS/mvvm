<?php

namespace Kriss\Mvvm\Session;

interface SessionInterface {
    public function get($key, $default);
    public function set($key, $value);
    public function remove($keys);
    public function start();
    public function isActive();
}
