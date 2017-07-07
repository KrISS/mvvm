<?php 

include_once 'ContainerTestTrait.php';

use Kriss\Core\Container\DiceContainer as Container;

class DiceContainerTest extends \PHPUnit\Framework\TestCase {
    use ContainerTestTrait;

    private function getContainer()
    {
        return new Container(new \Dice\Dice);
    }
}
