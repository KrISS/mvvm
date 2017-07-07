<?php

include_once 'ValidatorTestTrait.php';

use Kriss\Core\Validator\Validator as Validator;

class ValidatorTest extends \PHPUnit\Framework\TestCase {
    use ValidatorTestTrait;
    
    private function getValidator()
    {
        return new Validator;
    }

    public function testValidClosureValidator()
    {
        $validator = $this->getValidator();
        $closure = function($value, $data) {return true;};
        $validator->setConstraints('closure', [['closure', [$closure]]]);
        $this->assertSame(true, $validator->isValid([]));
    }

    public function testInvalidClosureValidator()
    {
        $validator = $this->getValidator();
        $closure = function($value, $data) {return false;};
        $validator->setConstraints('closure', [['closure', [$closure]]]);
        $this->assertSame(false, $validator->isValid([]));
    }
}