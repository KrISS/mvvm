<?php 

use Kriss\Core\Response\UnauthorizedResponse as Response;

class UnauthorizedResponseTest extends \PHPUnit\Framework\TestCase {
    public function testUnauthorizedResponse()
    {
        $_SERVER['SERVER_PROTOCOL'] = 'HTTP/1.0';
        $response = new Response();
        ob_start();
        $response->send();
        $content = ob_get_contents();
        ob_end_clean ();
        $this->assertSame('Not authorized', $content);
        $headers = xdebug_get_headers();
        $this->assertSame(401, http_response_code());
    }
}
