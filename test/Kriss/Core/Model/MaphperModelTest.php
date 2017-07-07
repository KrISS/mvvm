<?php 

include_once 'ModelTestTrait.php';

use Kriss\Core\Model\MaphperModel as Model;
use Maphper\Maphper as Maphper;
use Maphper\DataSource\Mock as Mock;

class MaphperModelTest extends \PHPUnit\Framework\TestCase {
    use ModelTestTrait;

    private function getModel($slug = 'data', $resultClass = null, $fakeData = []) {
        $maphper = new Maphper(new Mock(new ArrayObject(), 'id'));
        $model = new Model($maphper, $slug, $resultClass);
        foreach($fakeData as $data) {
            $model->persist($data);
        }
        return $model;
    }
}