<?php

use Kriss\Core\Controller\FormController as Controller;

class FormControllerTest extends \PHPUnit\Framework\TestCase {
    function testSuccessFormController()
    {
        $formViewModel = $this->getMockBuilder('Kriss\Mvvm\ViewModel\FormViewModelInterface')->getMock(); 
        $formViewModel->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $request->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue(''));
        $formAction = $this->getMockBuilder('Kriss\Mvvm\FormAction\FormActionInterface')->getMock();
        $formAction->expects($this->once())
            ->method('success');

        $formController = new Controller($formViewModel, $request, $formAction);
        $formController->action(); 
    }
    
    function testFailureFormController()
    {
        $formViewModel = $this->getMockBuilder('Kriss\Mvvm\ViewModel\FormViewModelInterface')->getMock(); 
        $formViewModel->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $request->expects($this->once())
            ->method('getRequest')
            ->will($this->returnValue(''));
        $formAction = $this->getMockBuilder('Kriss\Mvvm\FormAction\FormActionInterface')->getMock();
        $formAction->expects($this->once())
            ->method('failure');

        $formController = new Controller($formViewModel, $request, $formAction);
        $formController->action(); 
    }
}