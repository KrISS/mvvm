<?php
class ClassWithParams {
	public $foo;
	public $bar;
	public function __construct($foo, $bar) {
		$this->foo = $foo;
		$this->bar = $bar;
	}
	public function call($foo, $bar) {
		$this->foo = $foo;
		$this->bar = $bar;
	}
}

class FooClass {}

trait ContainerTestTrait {
    public function testCreateFoo()
    {
        $container = $this->getContainer();
        $this->getMockBuilder('FooClass')->getMock();
        $foo1 = $container->get('FooClass');
        $foo2 = $container->get('FooClass');
		$this->assertInstanceOf('FooClass', $foo1);
		$this->assertInstanceOf('FooClass', $foo2);
        $this->assertNotSame($foo1, $foo2);
    }

    public function testGetRules()
    {
        $container = $this->getContainer();
        $rule = ['shared' => true];
        $container->set('FooClass', $rule);
        $this->assertSame($rule, $container->getRule('FooClass'));
    }
    
    public function testCreateUnknownClass()
    {
        $container = $this->getContainer();
        $this->expectException('\Exception');
        $myobj = $container->get('Bar');
    }
    
    public function testSharedRule()
    {
        $container = $this->getContainer();
        $this->getMockBuilder('FooClass')->getMock();
        $container->set('FooClass', ['shared' => true]);
        $foo1 = $container->get('FooClass');
        $foo2 = $container->get('FooClass');
		$this->assertInstanceOf('FooClass', $foo1);
		$this->assertInstanceOf('FooClass', $foo2);
        $this->assertSame($foo1, $foo2);
    }

    public function testInstanceOfRule()
    {
        $container = $this->getContainer();
        $this->getMockBuilder('FooClass')->getMock();
        $container->set('Bar', ['instanceOf' => 'FooClass']);
        $this->assertInstanceOf('FooClass', $container->get('Bar'));
    }

    public function testConstructParamsRule()
    {
        $container = $this->getContainer();
        $container->set('ClassWithParams', ['constructParams' => ['foo', 'bar']]);
        $obj = $container->get('ClassWithParams');
        $this->assertSame($obj->foo, 'foo');
        $this->assertSame($obj->bar, 'bar');
    }

    public function testConstructParamsInstanceRule()
    {
        $container = $this->getContainer();
        $this->getMockBuilder('FooClass')->getMock();
        $container->set('ClassWithParams', ['constructParams' => [['instance' => 'FooClass'], 'bar']]);
        $obj = $container->get('ClassWithParams');
        $this->assertInstanceOf('FooClass', $obj->foo);
    }

    public function testCallRule()
    {
        $container = $this->getContainer();
        $container->set('ClassWithParams', ['constructParams' => ['bar', 'foo'], 'call' => [['call', ['foo', 'bar']]]]);
        $obj = $container->get('ClassWithParams');
        $this->assertSame($obj->foo, 'foo');
        $this->assertSame($obj->bar, 'bar');
    }
}
