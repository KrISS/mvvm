<?php
trait ViewTestTrait {
    public function testView()
    {
        $obj = new stdClass;
        $obj->foo = 'bar';
        $arr = ['foo' => 'bar'];
        $viewModel = $this->getMockBuilder('Kriss\Mvvm\ViewModel\ViewModelInterface')->getMock();
        $viewModel->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['slug-array' => $arr, 'slug-obj' => $obj]));

        $view = $this->getView($viewModel);
        $render = $view->render();
        
        $this->assertSame(true, is_array($render));
        $this->assertSame(2, count($render));
    }
}