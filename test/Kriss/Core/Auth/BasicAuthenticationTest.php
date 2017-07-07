<?php 

include_once 'AuthenticationTestTrait.php';

use Kriss\Core\Auth\BasicAuthentication as Authentication;

use Kriss\Mvvm\Auth\AuthenticationInterface;
use Kriss\Core\Session\Session;

class BasicAuthenticationTest extends \PHPUnit\Framework\TestCase {
    use AuthenticationTestTrait;

    private $request;
    private $session;
    
    private function getAuthentication($user) {
        $this->hashPassword =$this->getMockBuilder('Kriss\Mvvm\Auth\HashPasswordInterface')->getMock();
        $this->hashPassword->expects($this->any())
            ->method('hash')
            ->will($this->returnValue('hash'));
        $this->request =$this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $this->session = new Session;
        $userProvider =$this->getMockBuilder('Kriss\Mvvm\Auth\UserProviderInterface')->getMock();
        if (is_array($user) && $user['username'] == '') {
            $userProvider->expects($this->once())
                ->method('loadUser')
                ->with(['username' => 'user'])
                ->will($this->returnValue(null));
        } else {
            $userProvider->expects($this->once())
                ->method('loadUser')
                ->with(['username' => 'user'])
                ->will($this->returnValue($user));
        }
        
        return new Authentication($userProvider, $this->request, $this->session, $this->hashPassword);
    }

    private function login() {
        $this->request->expects($this->at(0))
            ->method('getServer')
            ->with('PHP_AUTH_USER')
            ->will($this->returnValue('user'));
        $this->request->expects($this->at(1))
            ->method('getServer')
            ->with('PHP_AUTH_PW')
            ->will($this->returnValue('hash'));
    }

    private function autoLogin() {
        $this->session->set('uid', 'uid');
        $this->session->set('username', 'user');
    }
    
    public function testLogout() {
        $user = new User;
        $authentication = $this->getAuthentication($user);
        $this->session->set('secret', 'secret');
        $this->login();
        $this->assertSame(AuthenticationInterface::wrongCredentials, $authentication->authenticate());
        $this->assertSame(false, $authentication->isAuthenticated());
        $this->assertSame(null, $authentication->getUser());
        $this->assertSame(null, $this->session->get('secret'));
    }
}