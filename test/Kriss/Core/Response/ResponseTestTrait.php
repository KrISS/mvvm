<?php
trait ResponseTestTrait {
    public function testDefaultResponse()
    {
        $response = $this->getResponse('hello', [['Location','http://localhost']]);
        ob_start();
        $response->send();
        $content = ob_get_contents();
        ob_end_clean ();
        $this->assertSame('hello', $content);
        $headers = xdebug_get_headers();
        $this->assertSame(1, count($headers));
        $this->assertSame('Location: http://localhost', reset($headers));
        
    }
}