<?php 

use Kriss\Core\Auth\HashPassword as HashPassword;

class HashPasswordTest extends \PHPUnit\Framework\TestCase {
    public function testHashPassword() {
        $hashPassword = new HashPassword;
        $password = 'secret';
        $this->assertNotSame($password, $hashPassword->hash($password));
    }
}