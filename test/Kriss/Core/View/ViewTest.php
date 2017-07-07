<?php 

include_once 'ViewTestTrait.php';

use Kriss\Core\View\View as View;

class ViewTest extends \PHPUnit\Framework\TestCase {
    use ViewTestTrait;
    
    private function getView($viewModel)
    {
        return new View($viewModel);
    }
}
