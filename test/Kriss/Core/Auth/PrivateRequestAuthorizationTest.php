<?php 

use Kriss\Core\Auth\PrivateRequestAuthorization as RequestAuthorization;

class PrivateRequestAuthorizationTest extends \PHPUnit\Framework\TestCase {
    public function testRequestAuthorizationTrue() {
        $authentication = $this->getMockBuilder('Kriss\Mvvm\Auth\AuthenticationInterface')->getMock();
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $request->expects($this->any())
            ->method('getPathInfo')
            ->with()
            ->will($this->returnValue('/login/'));
        $authorization = new RequestAuthorization($authentication, $request);
        $this->assertSame(true, $authorization->isGranted());
    }

    public function testRequestAuthorizationFalse() {
        $authentication = $this->getMockBuilder('Kriss\Mvvm\Auth\AuthenticationInterface')->getMock();
        $authentication->expects($this->once())
            ->method('isAuthenticated')
            ->with()
            ->will($this->returnValue(false));
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $request->expects($this->any())
            ->method('getPathInfo')
            ->with()
            ->will($this->returnValue('/secure/'));
        $authorization = new RequestAuthorization($authentication, $request);
        $this->assertSame(false, $authorization->isGranted());
    }
}