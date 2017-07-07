<?php

class Test {
    public $foo;
    public $bar;

    public function __construct($foo = 'foo', $bar = 'bar') {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}

trait ModelTestTrait {
    public function testSlugResultClass() {
        $model = $this->getModel('test', '\\stdClass');
        $this->assertSame('test', $model->getSlug());
        $this->assertSame('\\stdClass', $model->getResultClass());
    }
    
    public function testPersistRemove() {
        $model = $this->getModel();
        $this->assertSame('data', $model->getSlug());
        $this->assertSame(null, $model->getResultClass());
        
        $data = new stdClass();
        $data->foo = 'bar';
        $model->persist($data);
        $result = $model->getData();
        $this->assertSame(1, count($result));
        $this->assertSame($data->foo, reset($result)->foo);
        
        $model->persist($data);
        $this->assertSame(true, isset($data->id));
        
        $result = $model->getData();
        $this->assertSame(1, count($result));
        $this->assertSame($data->foo, reset($result)->foo);
        
        $model->remove($data);
        $result = $model->getData();
        $this->assertSame(0, count($result));
        
        $model->persist($data);
        $result = $model->getData();
        $this->assertSame(1, count($result));
    }
    
    public function testFindObject() {
        $model = $this->getModel('test', 'Test');
        
        $data = new Test();
        $model->persist($data);
        $result = $model->findOneBy();
        $this->assertInstanceOf('Test', $result);
        
        $data = new Test('bar', 'foo');
        $model->persist($data);
        $this->assertSame(true, isset($data->id));

        $result = $model->findOneBy();
        $this->assertSame(null, $result);

        $result = $model->findOneBy(['bar' => 'bar']);
        $this->assertInstanceOf('Test', $result);
        $this->assertSame('foo', $result->foo);

        $result = $model->findOneBy(['bar' => 'foo']);
        $this->assertInstanceOf('Test', $result);
        $this->assertSame('bar', $result->foo);

        $results = $model->findBy();
        $this->assertSame(2, count($results));
        
        $results = $model->findBy('foo');
        $this->assertSame(2, count($results));
    }

    public function testFlush() {
        $model = $this->getModel('test', 'Test');
        $data = new Test('bar', 'foo');
        $model->persist($data);
        $model->flush();

        $result = $model->getData();
        $this->assertSame(1, count($result));        
        
        $model = $this->getModel('test', 'Test', [$data]);
        $result = $model->getData();
        $this->assertSame(1, count($result));
        $model->remove(reset($result));
        $model->flush();

        $model = $this->getModel('test', 'Test');
        $result = $model->getData();
        $this->assertSame(0, count($result));
    }
    
    public function testRemoveAll() {
        $model = $this->getModel();
        
        $data1 = new stdClass();
        $data1->foo = 'bar';
        $model->persist($data1);
        $data2 = new stdClass();
        $data2->foo = 'bar';
        $model->persist($data2);
        
        $this->assertSame(2, $model->count());
        $model->remove();
        $this->assertSame(0, $model->count());
    }
}