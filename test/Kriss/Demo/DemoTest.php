<?php 

class DemoTest extends \PHPUnit\Framework\TestCase {
    public function testDefaultDemo() {
        ob_start();
        (new Kriss\Demo\App())->run();
        $content = ob_get_contents();
        ob_end_clean ();
        $this->assertSame(false, strpos($content, 'Invalid name'));
        $this->assertNotSame(false, strpos($content, 'Hello world'));
    }
    
    public function testHelloTontofClass() {
        $_GET = ['hello' => 'Tontof'];
        ob_start();
        (new Kriss\Demo\App())->run();
        $content = ob_get_contents();
        ob_end_clean ();
        $this->assertSame(false, strpos($content, 'Invalid name'));
        $this->assertNotSame(false, strpos($content, 'Hello Tontof'));
    }
    
    public function testHelloT0nt0fClass() {
        $_GET = ['hello' => 'T0nt0f'];
        ob_start();
        (new Kriss\Demo\App())->run();
        $content = ob_get_contents();
        ob_end_clean ();
        $this->assertNotSame(false, strpos($content, 'Invalid name'));
        $this->assertNotSame(false, strpos($content, 'Hello world'));
    }
}