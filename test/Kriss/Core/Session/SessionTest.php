<?php 

use Kriss\Core\Session\Session as Session;

class SessionTest extends \PHPUnit\Framework\TestCase {
    protected function setUp() {
        parent::setUp();
        $_SESSION = [];
    }

    function testSession()
    {
        $session = new Session('name');
        $this->assertEquals('name', session_name());
        $this->assertSame($session->isActive(), false);
        // autostart
        $session->set('foo', 'foo');
        $this->assertSame($session->isActive(), true);
        $this->assertSame('foo', $session->get('foo'));
        $this->assertSame('name', $session->get('name','name'));
        $session->remove('foo');
        $this->assertSame('bar', $session->get('foo','bar'));
    }
}
