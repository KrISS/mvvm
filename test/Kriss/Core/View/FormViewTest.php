<?php 

use Kriss\Core\View\FormView as View;

class FormViewTest extends \PHPUnit\Framework\TestCase {
    public function testFormView()
    {
        $form = [
            '*' => ['action' => '', 'method' => 'POST']
        ];
        $viewModel = $this->getMockBuilder('Kriss\Mvvm\ViewModel\FormViewModelInterface')->getMock();
        $viewModel->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['slug' => $form]));
        $viewModel->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(['error' => ['error']]));
        
        $view = new View($viewModel);
        $render = $view->render();
        
        $this->assertSame(true, is_array($render));
        $this->assertSame(2, count($render));
    }
    
    public function testDeleteFormView()
    {
        $form = [
            '*' => ['action' => '', 'method' => 'POST'],
            '_method' => ['type' => 'hidden', 'value' => 'DELETE']
        ];
        $viewModel = $this->getMockBuilder('Kriss\Mvvm\ViewModel\FormViewModelInterface')->getMock();
        $viewModel->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['slug' => $form]));
        $viewModel->expects($this->once())
            ->method('getErrors')
            ->will($this->returnValue(['error' => ['error']]));
        
        $view = new View($viewModel);
        $render = $view->render();
        
        $this->assertSame(true, is_array($render));
        $this->assertSame(2, count($render));
    }
}