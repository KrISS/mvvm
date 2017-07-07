<?php 

use Kriss\Core\Router\Router as Router;

class RouterTest extends \PHPUnit\Framework\TestCase {
    
    public function testGenerateNotFound() {
        $router = new Router();
        $this->expectException('\Exception');
        $router->generate('name');
    }
    
    public function testWrongMethodNotFound() {
        $router = new Router();
        $router->setRoute('simple', 'GET', '/', null);
        $this->expectException('\Exception');
        $router->dispatch('POST', '/');       
    }
    
    public function testAddSimpleRoute() {
        $router = new Router();
        $response = new stdClass;
        $router->setRoute('simple', 'GET', '/', $response);

        $this->assertSame($response, $router->dispatch('GET', '/'));
        $this->assertSame('/', $router->generate('simple'));
    }
    
    public function testOptionalHelloRoute() {
        $router = new Router();
        $router->setRoute('hello', 'GET', '/hello/<!name>', function ($name = "world") {
            return 'hello '.$name;
        });
        
        $this->assertSame('hello world', $router->dispatch('GET', '/hello/'));
        $this->assertSame([], $router->getRouteParameters());
        $this->assertSame('hello user', $router->dispatch('GET', '/hello/user'));
        $this->assertSame(['name' => 'user'], $router->getRouteParameters());
        $this->assertSame('/hello/', $router->generate('hello'));
        $this->assertSame('/hello/user', $router->generate('hello', ['name' => 'user']));
    }
    
    public function testMandatoryHelloRoute() {
        $router = new Router();
        $router->setRoute('hello', 'GET', '/hello/<name>', function ($name) {
            return 'hello '.$name;
        });
        
        $this->assertSame('/hello/user', $router->generate('hello', ['name' => 'user']));
        $this->assertSame('/hello/world?query=user&other=user', $router->generate('hello', ['name' => 'world', 'query' => 'user', 'other' => 'user']));
        $this->expectException('\Exception');
        $router->generate('hello');
    }
    
    public function testCorrectRegexParametersHelloRoute() {
        $router = new Router();
        $router->setRoute('hello', 'GET', '/hello/<name:[a-z]+>', function ($name) {
            return 'hello '.$name;
        });

        $this->assertSame('/hello/user', $router->generate('hello', ['name' => 'user']));
        $this->expectException('\Exception');
        $router->generate('hello', ['name' => 'user123']);
    }

    public function testRouteNotFound() {
        $router = new Router();
        $router->setRoute('hello', 'GET', '/hello', function () {
            return 'hello world';
        });
        
        $this->expectException('\Exception');
        $outputRoute = $router->dispatch('GET', '/hello-world');
    }

    public function testGetRoutes() {
        $router = new Router();
        $name = 'hello';
        $methods = ['GET'];
        $pattern = '/hello';
        $response = function () { return 'hello world'; };
        
        $router->setRoute($name, $methods, $pattern, $response);

        $routes = $router->getRoutes();
        $this->assertSame(1, count($routes));
        $this->assertSame(true, isset($routes[$name]));

        $expected = [$methods, $pattern, $response];
        $this->assertSame($expected, $routes[$name]);

        $this->assertSame($expected, $router->getRoutes($name));
    }
}
