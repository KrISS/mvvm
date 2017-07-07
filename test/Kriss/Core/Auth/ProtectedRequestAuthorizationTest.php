<?php 

use Kriss\Core\Auth\ProtectedRequestAuthorization as RequestAuthorization;

class ProtectedRequestAuthorizationTest extends \PHPUnit\Framework\TestCase {
    public function testRequestAuthorizationTrue() {
        $authentication = $this->getMockBuilder('Kriss\Mvvm\Auth\AuthenticationInterface')->getMock();
        $authentication->expects($this->any())
            ->method('isAuthenticated')
            ->with()
            ->will($this->returnValue(false));
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $request->expects($this->any())
            ->method('getMethod')
            ->with()
            ->will($this->returnValue('GET'));
        $authorization = new RequestAuthorization($authentication, $request);
        $this->assertSame(true, $authorization->isGranted());
    }

    public function testRequestAuthorizationFalse() {
        $authentication = $this->getMockBuilder('Kriss\Mvvm\Auth\AuthenticationInterface')->getMock();
        $authentication->expects($this->any())
            ->method('isAuthenticated')
            ->with()
            ->will($this->returnValue(false));
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $request->expects($this->any())
            ->method('getMethod')
            ->with()
            ->will($this->returnValue('POST'));
        $authorization = new RequestAuthorization($authentication, $request);
        $this->assertSame(false, $authorization->isGranted());
    }
}