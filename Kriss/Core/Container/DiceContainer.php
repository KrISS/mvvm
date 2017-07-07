<?php

namespace Kriss\Core\Container;

use Kriss\Mvvm\Container\ContainerInterface;

class DiceContainer implements ContainerInterface {
    private $dice;

    public function __construct(\Dice\Dice $dice) {$this->dice = $dice;}

    public function get($id, $args = []) {
        if ($this->has($id)) return $this->dice->create($id, $args);
        else throw new \Exception($id.' not found');
    }

    public function getRule($id) {return $this->dice->getRule($id);}

    public function has($id) {return (class_exists($id) || $this->dice->getRule($id) !== $this->dice->getRule('*'));}

    public function set($id, $rule = []) {$this->dice->addRule($id, $rule);}
}
