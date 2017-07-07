<?php 

use Kriss\Core\Router\RequestRouter as Router;

class RequestRouterTest extends \PHPUnit\Framework\TestCase {
    private $request = null;
    
    protected function setUp() {
        parent::setUp ();
        $this->request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $this->request->expects($this->once())
            ->method('getSchemeAndHttpHost')
            ->with()
            ->will($this->returnValue('https://localhost:1234'));
    }
    
    protected function tearDown() {
        $this->request = null;
        parent::tearDown ();
    }
    
    public function testAbsoluteUrl() {
        $router = new Router($this->request);
        $router->setRoute('simple', 'GET', '/foo', null);

        $this->assertSame('https://localhost:1234/foo', $router->generate('simple', []));
    }
}
