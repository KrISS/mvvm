<?php 

class KrissCoreRouterTest extends PHPUnit_Framework_TestCase {
    private $request = null;
	private $router = null;

	public function __construct() {
		parent::__construct();
	}
	
	protected function setUp() {
		parent::setUp ();
        $this->request = $this->getMockBuilder('Kriss\Core\Request\Request')->getMock();
        $this->router = new Kriss\Core\Router\Router($this->request);
	}

	protected function tearDown() {
        $this->request = null;
		$this->router = null;
		parent::tearDown ();
	}

	public function testResponseNotFound() {
        $this->setExpectedException('\Exception');
        $this->router->getResponse('GET', '/');
	}

	public function testGenerateNotFound() {
        $this->setExpectedException('\Exception');
        $this->router->generate('name');
	}

	public function testWrongMethodNotFound() {
        $this->setExpectedException('\Exception');
        $this->router->addResponse('simple', 'GET', '/', null);
        $this->router->getResponse('POST', '/');
	}

	public function testAddSimpleResponse() {
        $response = new stdClass;
        $this->router->addResponse('simple', 'GET', '/', $response);
        $outputResponse = $this->router->getResponse('GET', '/');

        $this->assertEquals($response, $outputResponse);
        $this->assertEquals('/', $this->router->generate('simple'));

        $this->request->expects($this->once())
            ->method('getSchemeAndHttpHost')
            ->with()
            ->will($this->returnValue('http://localhost'));
        $this->assertEquals('http://localhost/', $this->router->generate('simple', [], true));
	}

	public function testOptionalHelloResponse() {
        $this->router->addResponse('hello', 'GET', '/hello/<!name>', function ($name = "world") {
            return 'hello '.$name;
        });

        $outputResponse = $this->router->getResponse('GET', '/hello/');
        $this->assertEquals('hello world', $outputResponse);
        $this->assertEquals([], $this->router->getParameters());
        $outputResponse = $this->router->getResponse('GET', '/hello/user');
        $this->assertEquals('hello user', $outputResponse);
        $this->assertEquals(['name' => 'user'], $this->router->getParameters());
        $this->assertEquals('/hello/', $this->router->generate('hello'));
        $this->assertEquals('/hello/user', $this->router->generate('hello', ['name' => 'user']));

        $this->request->expects($this->once())
            ->method('getSchemeAndHttpHost')
            ->with()
            ->will($this->returnValue('http://localhost'));
        $this->assertEquals('http://localhost/hello/user', $this->router->generate('hello', ['name' => 'user'], true));
	}

	public function testMandatoryHelloResponse() {
        $this->router->addResponse('hello', 'GET', '/hello/<name>', function ($name) {
            return 'hello '.$name;
        });

        $this->assertEquals('/hello/user', $this->router->generate('hello', ['name' => 'user']));
        $this->assertEquals('/hello/world?query=user&other=user', $this->router->generate('hello', ['name' => 'world', 'query' => 'user', 'other' => 'user']));
        $this->setExpectedException('\Exception');
        $this->router->generate('hello');
	}
}
