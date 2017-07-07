<?php

use Kriss\Core\FormAction\PersistFormAction;
use Kriss\Core\FormAction\RemoveFormAction;
use Kriss\Core\FormAction\FirstTimeFormAction;

class FormActionTest extends \PHPUnit\Framework\TestCase {
    function testSuccessPersistFormAction()
    {
        $model = $this->getMockBuilder('Kriss\Mvvm\Model\ModelInterface')->getMock();
        $model->expects($this->at(0))
            ->method('persist');
        $model->expects($this->at(1))
            ->method('flush');

        $form = $this->getMockBuilder('Kriss\Mvvm\Form\FormInterface')->getMock();
        $form->expects($this->once())
            ->method('setFormData');
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['']));

        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $request->expects($this->once())
            ->method('getSchemeAndHttpHost');
        $request->expects($this->once())
            ->method('getBaseUrl');

        $formAction = new PersistFormAction($model, $form, $request);
        $formAction->success([]);
    }

    function testSuccessRemoveFormAction()
    {
        $model = $this->getMockBuilder('Kriss\Mvvm\Model\ModelInterface')->getMock();
        $model->expects($this->at(0))
            ->method('remove');
        $model->expects($this->at(1))
            ->method('flush');

        $form = $this->getMockBuilder('Kriss\Mvvm\Form\FormInterface')->getMock();
        $form->expects($this->once())
            ->method('setFormData');
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue(['']));

        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();
        $request->expects($this->once())
            ->method('getSchemeAndHttpHost');
        $request->expects($this->once())
            ->method('getBaseUrl');

        $formAction = new RemoveFormAction($model, $form, $request);
        $formAction->success([]);
    }

    function testFailurePersistFormAction()
    {
        $model = $this->getMockBuilder('Kriss\Mvvm\Model\ModelInterface')->getMock();
        $form = $this->getMockBuilder('Kriss\Mvvm\Form\FormInterface')->getMock();
        $form->expects($this->once())
            ->method('setData');
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();

        $formAction = new PersistFormAction($model, $form, $request);
        $formAction->failure([]);
    }

    function testFailureRemoveFormAction()
    {
        $model = $this->getMockBuilder('Kriss\Mvvm\Model\ModelInterface')->getMock();
        $form = $this->getMockBuilder('Kriss\Mvvm\Form\FormInterface')->getMock();
        $form->expects($this->once())
            ->method('setData');
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();

        $formAction = new RemoveFormAction($model, $form, $request);
        $formAction->failure([]);
    }

    function testSuccessFirstTimeFormAction()
    {
        $model = $this->getMockBuilder('Kriss\Mvvm\Model\ModelInterface')->getMock();
        $model->expects($this->at(0))
            ->method('count')
            ->will($this->returnValue(0));
        $model->expects($this->at(1))
            ->method('persist');
        $form = $this->getMockBuilder('Kriss\Mvvm\Form\FormInterface')->getMock();
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();

        $formAction = new FirstTimeFormAction($model, $form, $request);
        $formAction->success([]);
    }

    function testFailureFirstTimeFormAction()
    {
        $model = $this->getMockBuilder('Kriss\Mvvm\Model\ModelInterface')->getMock();
        $form = $this->getMockBuilder('Kriss\Mvvm\Form\FormInterface')->getMock();
        $form->expects($this->once())
            ->method('setData');
        $request = $this->getMockBuilder('Kriss\Mvvm\Request\RequestInterface')->getMock();

        $formAction = new FirstTimeFormAction($model, $form, $request);
        $formAction->failure([]);
    }
}