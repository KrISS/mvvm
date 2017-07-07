<?php 

include_once 'ViewTestTrait.php';

use Kriss\Core\View\JsonView as View;

class JsonViewTest extends \PHPUnit\Framework\TestCase {
    use ViewTestTrait;
    
    private function getView($viewModel)
    {
        return new View($viewModel);
    }
}
