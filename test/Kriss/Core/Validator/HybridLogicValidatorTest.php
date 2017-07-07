<?php

include_once 'ValidatorTestTrait.php';

use Kriss\Core\Validator\HybridLogicValidator as Validator;

class HybridLogicValidatorTest extends \PHPUnit\Framework\TestCase {
    use ValidatorTestTrait;
    
    private function getValidator()
    {
        return new Validator(new \HybridLogic\Validation\Validator());
    }
}