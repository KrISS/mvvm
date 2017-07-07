<?php

use Kriss\Core\ViewModel\FormViewModel as ViewModel;

class FormViewModelTest extends \PHPUnit\Framework\TestCase {
    private $criteria = 'criteria';
    private $orderBy = 'orderBy';
    private $offset = 'offset';
    private $limit = 'limit';

    private function getModel()
    {
        $model = $this->getMockBuilder('Kriss\Mvvm\Model\ModelInterface')->getMock();
        $model->expects($this->any())
            ->method('getSlug')
            ->will($this->returnValue('slug'));
        $model->expects($this->any())
            ->method('findBy')
            ->with($this->criteria, $this->limit, $this->offset, $this->orderBy)
            ->will($this->returnValue(['data']));
        
        return $model;
    }

    private function getForm()
    {
        $form = $this->getMockBuilder('Kriss\Mvvm\Form\FormInterface')->getMock();
        $form->expects($this->any())
            ->method('getAction')
            ->will($this->returnValue('action'));
        $form->expects($this->any())
            ->method('getMethod')
            ->will($this->returnValue('method'));
        $form->expects($this->any())
            ->method('getForm')
            ->will($this->returnValue(['form']));
        $form->expects($this->any())
            ->method('getData')
            ->will($this->returnValue('data'));

        return $form;
    }
    
    function getInvalidValidator()
    {        
        $validator = $this->getMockBuilder('Kriss\Mvvm\Validator\ValidatorInterface')->getMock();
        $validator->expects($this->any())
            ->method('getErrors')
            ->will($this->returnValue(['errors']));
        $validator->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(false));

        return $validator;
    }
    
    function getValidValidator()
    {        
        $validator = $this->getMockBuilder('Kriss\Mvvm\Validator\ValidatorInterface')->getMock();
        $validator->expects($this->any())
            ->method('getErrors')
            ->will($this->returnValue([]));
        $validator->expects($this->any())
            ->method('isValid')
            ->will($this->returnValue(true));

        return $validator;
    }
    
    function testFormViewModelWithValidValidator()
    {
        $viewModel = new ViewModel($this->getModel(), $this->getForm(), $this->getValidValidator());
        
        $this->assertSame(true, $viewModel->isValid([]));

        $viewModel->setOrderBy($this->orderBy);
        $viewModel->setOffset($this->offset);
        $viewModel->setLimit($this->limit);
        $viewModel->setCriteria($this->criteria);

        $this->assertSame(['slug' => 'slug', 'form' => ['form']], $viewModel->getData());
    }
    
    function testFormViewModelWithoutValidator()
    {
        $viewModel = new ViewModel($this->getModel(), $this->getForm());
        
        $this->assertSame(true, $viewModel->isValid([]));
        $this->assertSame([], $viewModel->getErrors());
    }
    
    function testFormViewModelWithInvalidValidator()
    {
        $viewModel = new ViewModel($this->getModel(), $this->getForm(), $this->getInvalidValidator());
        
        $this->assertSame(false, $viewModel->isValid([1 => [], 2 => []]));
        $this->assertSame([1 => ['errors'], 2 => ['errors']], $viewModel->getErrors());
    }
}