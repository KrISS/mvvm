<?php 

include_once 'AuthenticationTestTrait.php';

use Kriss\Core\Auth\SessionAuthentication as Authentication;

use Kriss\Mvvm\Auth\AuthenticationInterface;

use Kriss\Core\Session\Session;
use Kriss\Core\Request\Request;

class SessionAuthenticationTest extends \PHPUnit\Framework\TestCase {
    use AuthenticationTestTrait;
    
    private $session;
    private $inactivityTimeout = 42;

    private function getAuthentication($user) {
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
        $hashPassword =$this->getMockBuilder('Kriss\Mvvm\Auth\HashPasswordInterface')->getMock();
        $hashPassword->expects($this->any())
            ->method('hash')
            ->with('pass', 'user')
            ->will($this->returnValue('hash'));
        
        return new Authentication($userProvider, new Request, $this->session, $hashPassword, ['sessionName' => 'foo', 'inactivityTimeout' => $this->inactivityTimeout]);
    }

    private function login() {
        $_POST = ['username' => 'user', 'password' => 'pass'];
    }

    private function autoLogin() {
        $this->session->set('uid', 'uid');
        $this->session->set('username', 'user');
        $this->session->set('ip', '__');
        $this->session->set('expires_on', time() + $this->inactivityTimeout);
    }
    
    public function testLongLastingSession() {
        $user = ['username' => 'user', 'password' => 'hash'];
        $authentication = $this->getAuthentication($user);

        $this->login();
        $this->assertSame(AuthenticationInterface::authenticationSuccess, $authentication->authenticate());
        $this->assertSame(true, $authentication->isAuthenticated());
        $expiresOn = $this->session->get('expires_on', 0);
        $this->assertGreaterThanOrEqual((time()+$this->inactivityTimeout-$expiresOn), 0);

        $longLastingSession = 42;
        $this->session->set('longlastingsession', $longLastingSession);
        $this->assertSame(true, $authentication->isAuthenticated());
        $expiresOn = $this->session->get('expires_on', 0);
        $this->assertGreaterThanOrEqual((time()+$this->inactivityTimeout-$expiresOn), $longLastingSession);
        
    }
}