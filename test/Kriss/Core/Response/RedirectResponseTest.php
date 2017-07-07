<?php 

use Kriss\Core\Response\RedirectResponse as Response;

class RedirectResponseTest extends \PHPUnit\Framework\TestCase {
    public function testRedirectResponse()
    {
        $response = new Response('http://localhost:1234/');
        $response->send();
        $headers = xdebug_get_headers();
        $this->assertSame(1, count($headers));
        $this->assertSame('Location: http://localhost:1234/', reset($headers));
    }
}
