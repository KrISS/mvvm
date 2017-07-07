<?php 

include_once 'ContainerTestTrait.php';

use Kriss\Core\Container\Container as Container;

class ContainerTest extends \PHPUnit\Framework\TestCase {
    use ContainerTestTrait;

    private function getContainer()
    {
        return new Container;
    }
}
