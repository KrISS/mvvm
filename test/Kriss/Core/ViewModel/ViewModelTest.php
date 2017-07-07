<?php

use Kriss\Core\ViewModel\ViewModel as ViewModel;

class ViewModelTest extends \PHPUnit\Framework\TestCase {
    function testViewModel()
    {
        $criteria = 'criteria';
        $orderBy = 'orderBy';
        $offset = 24;
        $limit = 6;

        $model = $this->getMockBuilder('Kriss\Mvvm\Model\ModelInterface')->getMock();
        $model->expects($this->at(0))
            ->method('findBy');
        $model->expects($this->at(1))
            ->method('getSlug');
        $model->expects($this->at(2))
            ->method('findBy')
            ->with($criteria, $limit, $offset, $orderBy);

        $viewModel = new ViewModel($model);
        
        $viewModel->setCriteria($criteria);
        $viewModel->setOrderBy($orderBy);
        $viewModel->setOffset($offset);
        $viewModel->setLimit($limit);
        $viewModel->getData();
    }
}