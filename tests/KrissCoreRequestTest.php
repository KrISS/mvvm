<?php 

class KrissCoreRequestTest extends PHPUnit_Framework_TestCase {
	public function __construct() {
		parent::__construct();
	}
	
	protected function setUp() {
		parent::setUp ();
        $_SERVER = $this->getServer();
	}

	protected function tearDown() {
		parent::tearDown ();
	}

    // based on https://github.com/guzzle/psr7/blob/master/tests/ServerRequestTest.php
    private function getServer() {
        return [
            'PHP_SELF' => '/blog/article.php/bar',
            'GATEWAY_INTERFACE' => 'CGI/1.1',
            'SERVER_ADDR' => 'Server IP: 217.112.82.20',
            'SERVER_NAME' => 'www.blakesimpson.co.uk',
            'SERVER_SOFTWARE' => 'Apache/2.2.15 (Win32) JRun/4.0 PHP/5.2.13',
            'SERVER_PROTOCOL' => 'HTTP/1.0',
            'REQUEST_METHOD' => 'POST',
            'REQUEST_TIME' => 'Request start time: 1280149029',
            'QUERY_STRING' => 'id=10&user=foo',
            'DOCUMENT_ROOT' => '/path/to/your/server/root/',
            'HTTP_ACCEPT' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'HTTP_ACCEPT_CHARSET' => 'ISO-8859-1,utf-8;q=0.7,*;q=0.7',
            'HTTP_ACCEPT_ENCODING' => 'gzip,deflate',
            'HTTP_ACCEPT_LANGUAGE' => 'en-gb,en;q=0.5',
            'HTTP_CONNECTION' => 'keep-alive',
            'HTTP_HOST' => 'www.blakesimpson.co.uk',
            'HTTP_REFERER' => 'http://previous.url.com',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows; U; Windows NT 6.0; en-GB; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6 ( .NET CLR 3.5.30729)',
            'HTTPS' => '1',
            'REMOTE_ADDR' => '193.60.168.69',
            'REMOTE_HOST' => 'Client server\'s host name',
            'REMOTE_PORT' => '5390',
            'SCRIPT_FILENAME' => '/path/to/this/blog/article.php',
            'SERVER_ADMIN' => 'webmaster@blakesimpson.co.uk',
            'SERVER_PORT' => '80',
            'SERVER_SIGNATURE' => 'Version signature: 5.123',
            'SCRIPT_NAME' => '/blog/article.php',
            'REQUEST_URI' => '/blog/article.php/bar?id=10&user=foo',
        ];
    }

    function testNormalRequest()
    {
        $uri = 'http://www.blakesimpson.co.uk/blog/article.php/bar?id=10&user=foo';
        $request = new Kriss\Core\Request\Request();
        $this->assertEquals($uri, $request->getUri());

        $request = new Kriss\Core\Request\Request($uri);
        $this->assertEquals($uri, $request->getUri());
    }

    function testSecureRequest()
    {
        $uri = 'https://www.blakesimpson.co.uk/blog/article.php/bar?id=10&user=foo';
        $_SERVER = array_merge($_SERVER, ['HTTPS' => 'on', 'SERVER_PORT' => '443']);
        $request = new Kriss\Core\Request\Request();
        $this->assertEquals($uri, $request->getUri());

        $request = new Kriss\Core\Request\Request($uri);
        $this->assertEquals($uri, $request->getUri());
    }

    function testHostMissingRequest()
    {
        $uri = 'http://www.blakesimpson.co.uk/blog/article.php/bar?id=10&user=foo';
        $_SERVER = array_merge($_SERVER, ['HTTP_HOST' => null]);
        $request = new Kriss\Core\Request\Request();
        $this->assertEquals($uri, $request->getUri());

        $request = new Kriss\Core\Request\Request($uri);
        $this->assertEquals($uri, $request->getUri());
    }

    function testNoQueryStringRequest()
    {
        $uri = 'http://www.blakesimpson.co.uk/blog/article.php';
        $_SERVER = array_merge($_SERVER, ['REQUEST_URI' => '/blog/article.php', 'QUERY_STRING' => '']);
        $request = new Kriss\Core\Request\Request();
        $this->assertEquals($uri, $request->getUri());

        $request = new Kriss\Core\Request\Request($uri);
        $this->assertEquals($uri, $request->getUri());
    }

    function testDifferentPortRequest()
    {
        $uri = 'http://www.blakesimpson.co.uk:8324/blog/article.php/bar?id=10&user=foo';
        $_SERVER = array_merge($_SERVER, ['SERVER_PORT' => '8324']);
        $request = new Kriss\Core\Request\Request();
        $this->assertEquals($uri, $request->getUri());

        $request = new Kriss\Core\Request\Request($uri);
        $this->assertEquals($uri, $request->getUri());
    }

    function testRequestUriProxy()
    {        
        $uri = 'http://www.blakesimpson.co.uk/blog/article.php/bar?id=10&user=foo';
        $_SERVER = array_merge($_SERVER, ['REQUEST_URI' => $uri]);
        $request = new Kriss\Core\Request\Request();

        $this->assertEquals($uri, $request->getUri());
    }

    function testOrigPathInfo()
    {        
        $uri = 'http://www.blakesimpson.co.uk/blog/article.php/bar?id=10&user=foo';
        $_SERVER = array_merge($_SERVER, ['REQUEST_URI' => null, 'ORIG_PATH_INFO' => '/blog/article.php/bar']);
        $request = new Kriss\Core\Request\Request();

        $this->assertEquals($uri, $request->getUri());
    }

    function testPHPSelfOrigPathInfo()
    {        
        $uri = 'http://www.blakesimpson.co.uk/blog/article.php/bar?id=10&user=foo';
        $_SERVER = array_merge($_SERVER, ['SCRIPT_NAME' => null, 'PHP_SELF' => '/blog/article.php']);
        $request = new Kriss\Core\Request\Request();

        $this->assertEquals($uri, $request->getUri());
    }

    function testOrigScriptName()
    {        
        $uri = 'http://www.blakesimpson.co.uk/blog/article.php/bar?id=10&user=foo';
        $_SERVER = array_merge($_SERVER, ['SCRIPT_NAME' => null, 'ORIG_SCRIPT_NAME' => '/blog/article.php']);
        $request = new Kriss\Core\Request\Request();

        $this->assertEquals($uri, $request->getUri());
    }

    function testBacktrackBaseUrl()
    {        
        $uri = 'http://www.blakesimpson.co.uk/blog/article.php/bar?id=10&user=foo';
        $_SERVER = array_merge($_SERVER, ['SCRIPT_NAME' => null]);
        $request = new Kriss\Core\Request\Request();

        $this->assertEquals($uri, $request->getUri());
    }

    function testTruncatedRequestUriBaseUrl()
    {
        $uri = 'http://www.blakesimpson.co.uk/?id=10&user=foo';
        $request = new Kriss\Core\Request\Request($uri);

        $this->assertEquals($uri, $request->getUri());
    }

    function testEmptyBaseUrl()
    {
        $uri = 'http://www.blakesimpson.co.uk/?id=10&user=foo';
        $_SERVER = array_merge($_SERVER, [
            'SCRIPT_FILENAME' => '/path/to/this/article.php',
            'SCRIPT_NAME' => '/article.php',
            'REQUEST_URI' => '/?id=10&user=foo',
            'PHP_SELF' => '/article.php',
        ]);
        $request = new Kriss\Core\Request\Request();

        $this->assertEquals($uri, $request->getUri());
    }

    function testGetPost()
    {
        $_POST = [
            'name' => 'Pesho',
            'email' => 'pesho@example.com',
        ];
        $_GET = [
            'id' => 10,
            'user' => 'foo',
        ];

        $request = new Kriss\Core\Request\Request();

        $this->assertEquals('POST', $request->getMethod());        
        $this->assertEquals($_GET, $request->getQuery());        
        $this->assertEquals($_POST, $request->getRequest());        
    }
}
