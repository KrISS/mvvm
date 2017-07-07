<?php

namespace Kriss\Core\Model;

use Kriss\Mvvm\Model\ModelInterface;

class InMemoryModel implements ModelInterface {
    use ArrayModelTrait;
      
    protected $resultClass;
    protected $slug;
    protected $data = [];

    public function __construct($slug = "data", $resultClass = null, $data = []) {
        $this->resultClass = $resultClass;
        $this->slug = $slug;
        $this->data = $data;
    }
    
    public function flush() {}
}
