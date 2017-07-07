<?php 

use Kriss\Core\Container\Container as Container;
use Kriss\Core\App\App as App;

include_once('plugins/authBasic.php');

class authBasicTest extends \PHPUnit\Framework\TestCase {
    private function checkUnauthorizedResponse($app)
    {
        ob_start();
        $app->run();
        $content = ob_get_contents();
        ob_end_clean();
        
        $this->assertSame('Not authorized', $content);
        $headers = xdebug_get_headers();
        $this->assertSame(401, http_response_code());
        $this->assertSame('WWW-Authenticate: Basic realm="KrISS"', $headers[count($headers)-1]);
    }

    private function getApp($withAuth = false) {
        $app = new App(new Container());

        if ($withAuth) {
            $app->addPlugin('authBasic');
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
    
    public function testBasicUnauthorizedResponseApp()
    {
        $app = $this->getApp(true);

        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->checkUnauthorizedResponse($app);
    }
    
    public function testLoginApp()
    {
        $app = $this->getApp(true);
        
        $_SERVER['REQUEST_URI'] = '/login/';
        $this->checkUnauthorizedResponse($app);
    }
    
    public function testAuthenticatedLoginApp()
    {
        $app = $this->getApp(true);

        $_SERVER['PHP_AUTH_USER'] = 'username';
        $_SERVER['PHP_AUTH_PW'] = 'password';
        $_SERVER['REQUEST_URI'] = '/login/';
        
        ob_start();
        $app->run();
        $content = ob_get_contents();
        ob_end_clean();
        
        $headers = xdebug_get_headers();
        $this->assertSame('Location: http://localhost', $headers[count($headers)-1]);
    }
    
    public function testAuthenticatedLogoutApp()
    {
        $app = $this->getApp(true);

        $_SERVER['PHP_AUTH_USER'] = 'username';
        $_SERVER['PHP_AUTH_PW'] = 'password';
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
        $app->addPlugin('authBasic');
        
        ob_start();
        $app->run();
        $content = ob_get_contents();
        ob_end_clean();
        
        $headers = xdebug_get_headers();
        $this->assertSame('Location: http://localhost/admin/new/', $headers[count($headers)-1]);
    }
}
