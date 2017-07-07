<?php 

use Kriss\Core\Container\Container as Container;
use Kriss\Core\App\App as App;

include_once('plugins/authSession.php');

class authSessionTest extends \PHPUnit\Framework\TestCase {
    private function checkRedirectResponse($app)
    {
        ob_start();
        $app->run();
        $content = ob_get_contents();
        ob_end_clean();
        
        $headers = xdebug_get_headers();
        $router = $app->getContainer()->get('Router');

        $ref = 'Location: '.$router->generate('login');
        $this->assertSame($ref, substr($headers[count($headers)-1], 0, strlen($ref)));
    }

    private function getApp($withAuth = false) {
        $app = new App(new Container());

        if ($withAuth) {
            $app->addPlugin('authSession');
            $app->getContainer()->set('#admin_model', [
                'instanceOf' => 'Kriss\\Core\\Model\\InMemoryModel',
                'shared' => true,
                'constructParams' => [
                    'admin',
                    null,
                    [['username' => 'username', 'password' => sha1('passwordusername')]]
                ]
            ]);
        }
        
        return $app;
    }
    
    public function testDefaultLoginExceptionApp()
    {
        $app = $this->getApp();
        
        $this->expectException('\Exception');
        
        $_SERVER['REQUEST_URI'] = '/login/';
        $app->run();
    }

    public function testDefaultLogoutExceptionApp()
    {
        $app = $this->getApp();
        
        $this->expectException('\Exception');
        
        $_SERVER['REQUEST_URI'] = '/logout/';
        $app->run();
    }
    
    public function testRedirectResponseApp()
    {
        $app = $this->getApp(true);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->checkRedirectResponse($app);
    }
    
    public function testLoginApp()
    {
        $app = $this->getApp(true);
        
        $_SERVER['REQUEST_URI'] = '/login/';
        
        ob_start();
        $app->run();
        $content = ob_get_contents();
        ob_end_clean();
        
        $this->assertNotSame(false, strpos($content, '</form>'));
        $this->assertNotSame(false, strpos($content, 'name="username"'));
        $this->assertNotSame(false, strpos($content, 'name="password"'));
    }
    
    public function testAuthenticatedLoginApp()
    {
        $app = $this->getApp(true);

        // simulate authentication
        $_POST['username'] = 'username';
        $_POST['password'] = 'password';
        $_SERVER['REQUEST_URI'] = '/login/';
        $_SERVER['REQUEST_METHOD'] = 'POST';
        
        ob_start();
        $app->run();
        $content = ob_get_contents();
        ob_end_clean();
        
        $headers = xdebug_get_headers();
        $this->assertSame('Location: http://localhost', $headers[count($headers)-1]);
    }
    
    public function testLogoutApp()
    {
        $app = $this->getApp(true);
        
        // simulate already authenticated
        $session = $app->getContainer()->get('Session');
        $session->start();
        $session->set('ip', '__');
        $session->set('expires_on', time()+3600);
        $session->set('uid', 'uid');
        $session->set('username', 'username');
            
        $_SERVER['REQUEST_URI'] = '/logout/';

        ob_start();
        $app->run();
        $content = ob_get_contents();
        ob_end_clean();
        
        $headers = xdebug_get_headers();
        $this->assertSame('Location: http://localhost', $headers[count($headers)-1]);
    }
    
    public function testInstallApp()
    {
        $app = $this->getApp();
        $app->addPlugin('authSession');
        
        ob_start();
        $app->run();
        $content = ob_get_contents();
        ob_end_clean();
        
        $headers = xdebug_get_headers();
        $this->assertSame('Location: http://localhost/admin/new/', $headers[count($headers)-1]);
    }
}
