<?php

namespace Kriss\Demo;

use Kriss\Mvvm\Model\ModelInterface;

class Model implements ModelInterface {
    protected $data = 'world';
    
    public function getData() { return $this->helloWorld(); }
        
    public function getResultClass() { return null; }
    
    public function getSlug() { return 'hello-world'; }
    
    public function persist($data) {$this->data = $data;}
    
    public function remove($data) {$this->data = 'world';}
    
    public function flush() {}

    private function helloWorld() {return 'Hello '.$this->data;}
}
