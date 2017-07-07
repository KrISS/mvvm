<?php

namespace Kriss\Core\Model;

use Kriss\Mvvm\Model\ModelInterface;

class MaphperModel implements ModelInterface {
    protected $resultClass;
    protected $slug;

    public function __construct(\Maphper\Maphper $maphper, $slug = "data", $resultClass = null) {
        $this->resultClass = $resultClass;
        $this->slug = $slug;
        if (!is_null($resultClass)) {
            $maphper = $maphper->resultClass($resultClass);
        }
        $this->data = $maphper;
    }

    public function getResultClass() {
        return $this->resultClass;
    }

    public function getData() {
        $result = [];
        foreach($this->data as $data) {
            $result[] = $data;
        }
        return $result;
    }

    public function findOneBy($criteria = []) {
        $result = $this->findBy($criteria);
        if (count($result) === 1) {
            return reset($result);
        }

        return null;
    }

    public function count($criteria = []) {return count($this->data->filter($criteria));}

    public function findBy($criteria = [], $limit = null, $offset = null, $orderBy = null) {
        $result = [];
        if (!is_array($criteria)) {
            $fakeData = new $this->resultClass;
            $search = $criteria;
            $criteria = [\Maphper\Maphper::FIND_OR => []];
            foreach(array_keys((array)$fakeData) as $key) {
                $criteria[\Maphper\Maphper::FIND_OR][] = [$key => $search];
            }
        }
        foreach($this->data->filter($criteria)->sort($orderBy)->offset($offset)->limit($limit) as $data) { $result[$data->id] = $data; }

        return $result;
    }

    public function remove($data = null) {
        if (count($this->data) > 0) {
            if (is_null($data)) {
                $this->data->delete();
            } else {
                unset($this->data[$data->id]);
            }
        }
    }

    public function persist($data) {
        if (is_null($this->resultClass) || $data instanceOf $this->resultClass) {
            $this->data[] = $data;
        }
    }

    public function getSlug() {return $this->slug;}

    public function flush() {}
}
