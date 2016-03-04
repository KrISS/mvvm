<?php

include('autoload.php');

class Test {
    public $test = 'default_test';
}

// go to mini.php/test
(new Kriss\Rest\App\RestApp(new Kriss\Core\Container\DiceContainer(new Dice\Dice)))->run();

