<?php

namespace Kriss\Mvvm\ViewModel;

interface ViewModelInterface {
    public function getData();
    public function setCriteria($criteria);
    public function setOrderBy($orderBy);
    public function setOffset($offset);
    public function setLimit($limit);
}
