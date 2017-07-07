<?php 

include_once 'ViewTestTrait.php';

use Kriss\Core\View\VarDumpView as View;

class VarDumpViewTest extends \PHPUnit\Framework\TestCase {
    use ViewTestTrait;
    
    private function getView($viewModel)
    {
        return new View($viewModel);
    }
}

