<?php 

use Kriss\Core\Auth\Authorization as Authorization;

class AuthorizationTest extends \PHPUnit\Framework\TestCase {
    public function testAuthorization() {
        $authentication = $this->getMockBuilder('Kriss\Mvvm\Auth\AuthenticationInterface')->getMock();
        $authentication->expects($this->once())
            ->method('isAuthenticated')
            ->with()
            ->will($this->returnValue(true));
        $authorization = new Authorization($authentication);
        $this->assertSame(true, $authorization->isGranted());
    }
}