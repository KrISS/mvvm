<?php

namespace Kriss\Mvvm\Container;

interface ContainerInterface {
    public function has($id);
    public function get($id);
    public function getRule($id);
    public function set($id, $rule = []);
}
