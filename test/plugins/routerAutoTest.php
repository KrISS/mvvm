<?php 

use Kriss\Core\Container\Container as Container;
use Kriss\Core\Response\Response;
use Kriss\Core\App\App as App;

include_once 'plugins/routerAuto.php';

class AutoRouteFoo {
    public $foo = 'foo';
    public $bar = 'bar';
}

class routerAutoTest extends \PHPUnit\Framework\TestCase {
    private $container;
    
    private function dispatch($method, $pathInfo) {
        $router = $this->container->get('Router');
        ob_start();
        $router->dispatch($method, $pathInfo)->send();
        $body = ob_get_contents();
        ob_end_clean();

        return $body;
    }

    public function testDefaultRouterAuto()
    {
        $this->container = new Container();
        $app = new App($this->container);

        $app->addPlugin('routerAuto');

        $this->assertSame($this->container, $app->getContainer());
        
        $this->container->set('$autoroutefoo_index_view', [
            'instanceOf' => 'Kriss\\Core\\View\\JsonView',
        ]);
        
        $this->container->set('Router', [
            'call' => [['setRoute', ['index', 'GET', '/', function () {return new Response('');}]]]
        ]);
        
        ob_start();
        $app->run();
        $body = ob_get_contents();
        ob_end_clean();

        $this->assertSame(['slug' => 'autoroutefoo', 'data' => []], json_decode($this->dispatch('GET', '/autoroutefoo/'), true));
    }
    
    public function testRouterAuto()
    {
        if (file_exists('test/foo.php')) unlink('test/foo.php');
        if (file_exists('test/foos.php')) unlink('test/foos.php');
        
        $this->container = new Container();
        $app = new App($this->container);
        
        $app->addPlugin('routerAuto');
        $app->configPlugin('routerAuto', [['foo' => 'AutoRouteFoo'], ['foos' => 'AutoRouteFoo']]);
        
        $this->container->set('#foo_model', [
            'instanceOf' => 'Kriss\\Core\\Model\\ArrayModel',
            'constructParams' => [
                'foo',
                'AutoRouteFoo',
                'test',
            ]
        ]);
        $this->container->set('#foos_model', [
            'instanceOf' => 'Kriss\\Core\\Model\\ArrayModel',
            'constructParams' => [
                'foos',
                'AutoRouteFoo',
                'test',
            ]
        ]);
        $this->container->set('$autoroutefoo_view', [
            'instanceOf' => 'Kriss\\Core\\View\\JsonView',
        ]);
        $this->container->set('#foos_view', [
            'instanceOf' => 'Kriss\\Core\\View\\JsonView',
        ]);
        
        ob_start();
        $app->run();
        $body = ob_get_contents();
        ob_end_clean();

        $this->assertSame(['slug' => 'foo', 'data' => []], json_decode($this->dispatch('GET', '/foo/'), true));
        $this->assertSame(['slug' => 'foos', 'data' => []], json_decode($this->dispatch('GET', '/foos/'), true));
        
        $_POST = ['foo' => 'bar1'];
        $this->dispatch('POST', '/foo/');
        $this->dispatch('POST', '/foos/');
        $_POST = ['foo' => 'bar2'];
        $this->dispatch('POST', '/foo/');
        $this->dispatch('POST', '/foos/');

        $foo = json_decode($this->dispatch('GET', '/foo/'), true);
        $foos = json_decode($this->dispatch('GET', '/foos/'), true);
        $this->assertSame(1, count($foo['data']));
        $this->assertSame(2, count($foos['data']));
        $this->assertSame('bar2', $foo['data'][1]['foo']);
        $this->assertSame('bar1', $foos['data'][1]['foo']);
        $this->assertSame('bar2', $foos['data'][2]['foo']);
        
        $_POST = ['foo' => 'bar'];
        $this->dispatch('PUT', '/foo/');
        $this->dispatch('PUT', '/foos/1/');
        $foo = json_decode($this->dispatch('GET', '/foo/'), true);
        $foos1 = json_decode($this->dispatch('GET', '/foos/1/'), true);
        $this->assertSame('bar', $foo['data'][1]['foo']);
        $this->assertSame('bar', $foos1['data'][1]['foo']);

        $formAutoRouteFoo = json_decode($this->dispatch('GET', '/foo/new/'), true);
        $formAutoRouteFoos = json_decode($this->dispatch('GET', '/foos/new/'), true);
        $this->assertSame('http://localhost/foo/', $formAutoRouteFoo['form']['*']['action']);
        $this->assertSame('POST', $formAutoRouteFoo['form']['*']['method']);
        $this->assertSame('foo', $formAutoRouteFoo['form']['foo']['value']);
        $this->assertSame(false, isset($formAutoRouteFoo['_method']));
        $this->assertSame('http://localhost/foos/', $formAutoRouteFoos['form']['*']['action']);
        $this->assertSame('POST', $formAutoRouteFoos['form']['*']['method']);
        $this->assertSame('foo', $formAutoRouteFoos['form']['foo']['value']);
        $this->assertSame(false, isset($formAutoRouteFoos['_method']));


        $_GET = ['search' => 'bar2'];
        $formAutoRouteFoos = json_decode($this->dispatch('GET', '/foos/'), true);
        $this->assertSame(1, count($formAutoRouteFoos['data']));
        $_GET = [];
        
        $formAutoRouteFoo = json_decode($this->dispatch('GET', '/foo/edit/'), true);
        $formAutoRouteFoos = json_decode($this->dispatch('GET', '/foos/1/edit/'), true);
        $this->assertSame('http://localhost/foo/', $formAutoRouteFoo['form']['*']['action']);
        $this->assertSame('POST', $formAutoRouteFoo['form']['*']['method']);
        $this->assertSame('bar', $formAutoRouteFoo['form']['foo']['value']);
        $this->assertSame('PUT', $formAutoRouteFoo['form']['_method']['value']);
        $this->assertSame('http://localhost/foos/1/', $formAutoRouteFoos['form']['*']['action']);
        $this->assertSame('POST', $formAutoRouteFoos['form']['*']['method']);
        $this->assertSame('bar', $formAutoRouteFoos['form']['foo']['value']);
        $this->assertSame('PUT', $formAutoRouteFoos['form']['_method']['value']);

        $formAutoRouteFoo = json_decode($this->dispatch('GET', '/foo/delete/'), true);
        $formAutoRouteFoos = json_decode($this->dispatch('GET', '/foos/1/delete/'), true);
        $this->assertSame('http://localhost/foo/', $formAutoRouteFoo['form']['*']['action']);
        $this->assertSame('POST', $formAutoRouteFoo['form']['*']['method']);
        $this->assertSame('DELETE', $formAutoRouteFoo['form']['_method']['value']);
        $this->assertSame('http://localhost/foos/1/', $formAutoRouteFoos['form']['*']['action']);
        $this->assertSame('POST', $formAutoRouteFoos['form']['*']['method']);
        $this->assertSame('DELETE', $formAutoRouteFoos['form']['_method']['value']);
        
        
        $this->dispatch('DELETE', '/foo/', true);
        $this->dispatch('DELETE', '/foos/1/', true);
        $this->dispatch('DELETE', '/foos/', true);
    }

    public function testRouterAutoView()
    {
        $this->container = new Container();
        $app = new App($this->container);

        $app->addPlugin('routerAuto');
        $app->configPlugin('routerAuto', [['single' => 'AutoRouteFoo'], ['list' => 'AutoRouteFoo']]);

        $this->assertSame($this->container, $app->getContainer());
        
        $this->container->set('#single_validator', [
            'instanceOf' => 'Kriss\\Core\\Validator\\Validator',
            'call' => [
                ['setConstraints', [
                    'foo', [['minLength', [4], 'foo too short']],
                ]],
            ],
        ]);
        $this->container->set('#single_model', [
            'instanceOf' => 'Kriss\\Core\\Model\\InMemoryModel',
            'constructParams' => [
                'single',
                'AutoRouteFoo',
            ]
        ]);
        $this->container->set('#list_model', [
            'instanceOf' => 'Kriss\\Core\\Model\\InMemoryModel',
            'constructParams' => [
                'list',
                'AutoRouteFoo',
            ]
        ]);
        
        ob_start();
        $app->run();
        $body = ob_get_contents();
        ob_end_clean();
        
        $single = $this->dispatch('POST', '/single/');
        $this->assertNotSame(false, strpos($single, 'foo too short'));
        $_POST = ['foo' => 'long'];
        $single = $this->dispatch('POST', '/single/');
        $this->assertSame(false, strpos($single, 'foo too short'));
        $_POST = [];
        
        $this->dispatch('POST', '/list/');
        $this->dispatch('POST', '/list/');
        $this->dispatch('POST', '/list/');
        
        $single = $this->dispatch('GET', '/single/');
        $_GET = ['limit' => 1, 'page' => 2];
        $list = $this->dispatch('GET', '/list/');
        $_GET = [];
        
        $this->assertNotSame(false, strpos($single, 'id="single"'));
        $this->assertNotSame(false, strpos($list, 'id="list"'));
        $this->assertNotSame(false, strpos($list, 'id="pagination-list"'));
        
        $single = $this->dispatch('GET', '/single/delete/');
        $list = $this->dispatch('GET', '/list/delete/');
        
        $this->assertNotSame(false, strpos($single, 'value="DELETE"'));
        $this->assertNotSame(false, strpos($list, 'value="DELETE"'));
    }
}
