<?php 

include_once 'ModelTestTrait.php';

use Kriss\Core\Model\InMemoryModel as Model;

class InMemoryModelTest extends \PHPUnit\Framework\TestCase {
    use ModelTestTrait;

    private function getModel($slug = 'data', $resultClass = null, $data = []) {
        return new Model($slug, $resultClass, $data);
    }
}