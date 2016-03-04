<?php

namespace Kriss\Core\Model;

use Kriss\Mvvm\Model\ListModelInterface;

class ArrayModel extends Model implements ListModelInterface {
    protected $data = [];

    public function findOneBy($criteria = null)
    {
        if (is_null($criteria)) return null;

        if (isset($this->data[$criteria])) {
            return $this->data[$criteria];
        } else {
            return null;
        }
    }

    public function findBy($criteria = null, $orderBy = null, $limit = null, $offset = null)
    {
        if (is_null($criteria)) return $this->data;

        if (isset($this->data[$criteria])) {
            return [$this->data[$criteria]];
        } else {
            return [];
        }
    }

    public function persist($data) {
        $index = array_search($data, $this->data, true);
        if ($index !== false) {
            $this->data[$index] = $data;
        } else {
            if (empty($this->data)) { $this->data[1] = $data; }
            else { $this->data[] = $data; }
            $index = array_search($data, $this->data, true);
            $data->id = $index;
        }
    }

    public function remove($data) {
        $index = array_search($data, $this->data);
        if ($index !== false) {
            unset($this->data[$index]);
        }
    }
}
