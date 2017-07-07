<?php 

use Kriss\Core\Container\Container as Container;
use Kriss\Core\App\App as App;

include_once 'plugins/responseException.php';

class responseExceptionTest extends \PHPUnit\Framework\TestCase {

    public function testDefaultExceptionApp()
    {
        $this->container = new Container();
        $app = new App($this->container);
        
        $this->expectException('\Exception');
        
        $_SERVER['REQUEST_URI'] = '/exception';
        $app->run();
    }
    
    public function testNoExceptionApp()
    {
        $this->container = new Container();
        $app = new App($this->container);
        
        $app->addPlugin('responseException');
        
        $_SERVER['REQUEST_URI'] = '/exception';
        ob_start();
        $app->run();
        $body = ob_get_contents();
        ob_end_clean();

        $this->assertSame($body, 'GET: /exception not found');
    }
}
