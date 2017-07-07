<?php
  
namespace Kriss\Core\ViewModel;

trait ViewModelTrait {
    protected $model;
    protected $criteria = [];
    protected $orderBy = null;
    protected $offset = null;
    protected $limit = null;
    
    public function setCriteria($criteria) {$this->criteria = $criteria;}
    public function setOrderBy($orderBy) {$this->orderBy = $orderBy;}
    public function setOffset($offset) {$this->offset = $offset;}
    public function setLimit($limit) {$this->limit = $limit;}
}
