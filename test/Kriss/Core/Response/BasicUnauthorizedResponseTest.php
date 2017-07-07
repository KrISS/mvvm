<?php 

use Kriss\Core\Response\BasicUnauthorizedResponse as Response;

class BasicUnauthorizedResponseTest extends \PHPUnit\Framework\TestCase {
    public function testBasicUnauthorizedResponse()
    {
        $session = $this->getMockBuilder('Kriss\Mvvm\Session\SessionInterface')->getMock();
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $request->expects($this->once())
            ->method('getServer')
            ->with('SERVER_PROTOCOL')
            ->will($this->returnValue('HTTP/1.0'));

        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
        $response = new Response($session, $request);
        ob_start();
        $response->send();
        $content = ob_get_contents();
        ob_end_clean ();
        $this->assertSame('Not authorized', $content);
        $headers = xdebug_get_headers();
        $this->assertSame(401, http_response_code());
        $this->assertSame(1, count($headers));
        $this->assertSame('WWW-Authenticate: Basic realm="KrISS"', reset($headers));        
     }
}
