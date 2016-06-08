<?php

namespace Kriss\Core\Model;

use Kriss\Mvvm\Model\ListModelInterface;

class MaphperModel implements ListModelInterface {
    protected $resultClass;
    protected $slug;

    protected $toPersist = [];
    protected $toRemove = [];

    public function __construct($slug = "data", $resultClass = null, \Maphper\Maphper $maphper) { 
        $this->resultClass = $resultClass;
        $this->slug = $slug;
        $this->data = $maphper;
    }

    public function getResultClass() {
        return $this->resultClass;
    }

    public function getData() {
        return $this->data;
    }

    public function findOneBy($criteria = null) {
        if (is_null($criteria)) {
            $result = [];
            foreach($this->data as $data) {
                $result[] = $data;
            }

            return $result;
        } else {
            if (is_array($criteria)) {
                return $this->data->filter($criteria)->limit(1)->item(0);
            } else {
                return $this->data[$criteria];
            }
        }
    }

    public function findBy($criteria = null, $orderBy = null, $limit = null, $offset = null)
    {
        if (is_null($criteria)) {
            $result = [];
            foreach($this->data as $data) { $result[$data->id] = $data; }
            return $result;
        }

        if (isset($this->data[$criteria])) {
            return [$this->data[$criteria]];
        } else {
            return [];
        }
    }

    public function remove($data) {
        $this->toRemove[] = $data;
    }

    public function persist($data) {
        $this->toPersist[] = $data;
    }

    public function getSlug() {
        return $this->slug;
    }

    public function flush() {
        foreach($this->toPersist as $data) {
            $this->data[] = $data;
        }
        $this->toPersist = [];
        foreach($this->toRemove as $data) {
            unset($this->data[$data->id]);
        }
        $this->toRemove = [];
    }
}
