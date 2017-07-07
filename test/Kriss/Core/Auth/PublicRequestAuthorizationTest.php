<?php 

use Kriss\Core\Auth\PublicRequestAuthorization as RequestAuthorization;

class PublicRequestAuthorizationTest extends \PHPUnit\Framework\TestCase {
    public function testRequestAuthorizationTrue() {
        $authentication = $this->getMockBuilder('Kriss\Mvvm\Auth\AuthenticationInterface')->getMock();
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $authorization = new RequestAuthorization($authentication, $request);
        $this->assertSame(true, $authorization->isGranted());
    }
}