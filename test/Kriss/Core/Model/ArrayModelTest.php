<?php 

include_once 'ModelTestTrait.php';

use Kriss\Core\Model\ArrayModel as Model;

class ArrayModelTest extends \PHPUnit\Framework\TestCase {
    use ModelTestTrait;

    private function getModel($slug = 'data', $resultClass = null) {
        return new Model($slug, $resultClass, 'test');
    }
    
    public function testFindArray() {
        $model = $this->getModel('test');
        
        $data = ['foo' => 'foo', 'bar' => 'bar'];
        $model->persist($data);
        $result = $model->findOneBy();
        $this->assertSame($data, $result);
        
        $data = ['foo' => 'bar', 'bar' => 'foo'];
        $data = $model->persist($data);
        $this->assertSame(true, isset($data['id']));

        $result = $model->findOneBy();
        $this->assertSame(null, $result);

        $result = $model->findOneBy(['bar' => 'bar']);
        $this->assertSame('foo', $result['foo']);

        $result = $model->findOneBy(['bar' => 'foo']);
        $this->assertSame('bar', $result['foo']);

        $results = $model->findBy();
        $this->assertSame(2, count($results));
        
        $results = $model->findBy('foo');
        $this->assertSame(2, count($results));
    }
}