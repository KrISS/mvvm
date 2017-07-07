<?php

namespace Kriss\Core\Model;

trait ArrayModelTrait {

    public function count($criteria = []) {return empty($criteria)?count($this->data):count($this->findBy($criteria));}
    
    public function getData() {return $this->data;}
    
    public function getResultClass() {return $this->resultClass;}
    
    public function getSlug() {return $this->slug;}
    
    public function findOneBy($criteria = []) {
        $result = $this->findBy($criteria);
        if (count($result) === 1) {
            return reset($result);
        }

        return null;
    }

    public function findBy($criteria = [], $limit = null, $offset = null, $orderBy = null) {
        if (empty($criteria) && is_null($limit) && is_null($offset) && is_null($orderBy)) return $this->data;

        $readProperty = function($property) { return $this->$property; };
        $filter = function($data) use ($criteria, $readProperty) {
            $keep = true;

            if (is_array($criteria)) {
                if (is_object($data)) {
                    $readProperty = $readProperty->bind($readProperty, $data, get_class($data));
                    foreach($criteria as $key => $value) {
                        if ($readProperty($key) != $value) $keep = false;
                    }
                } else {
                    foreach($criteria as $key => $value) {
                        if ($data[$key] != $value) $keep = false;
                    }
                }
            } else {
                $keep = false;
                if (is_object($data)) {
                    foreach(((array)$data) as $key => $value) {
                        if (strpos($data->$key, $criteria) !== false) $keep = true;
                    }
                } else {
                    foreach($data as $key => $value) {
                        if (strpos($data[$key], $criteria) !== false) $keep = true;
                    }
                }

            }
            return $keep;
        };
        return array_slice(array_filter($this->data, $filter), $offset, $limit, true);
    }
    
    public function persist($data) {
        $index = array_search($data, $this->data, true);
        if ($index !== false) {
            $this->data[$index] = $data;
        } else {
            if (empty($this->resultClass) || $data instanceof $this->resultClass) {
                if (empty($this->data)) {
                    $this->data[1] = $data;
                } else {
                    $this->data[] = $data;
                }
                $id = array_search($data, $this->data, true);
                if (is_array($data)) $data['id'] = $id;
                else $data->id = $id;
            }
        }

        return $data;
    }

    public function remove($data = null) {
        if (is_null($data)) {
            $this->data = [];
        } else {
            $index = array_search($data, $this->data);
            if ($index !== false) {
                unset($this->data[$index]);
            }
        }
    }
    
    /** @codeCoverageIgnore */
    private function createDir() {
        $dir = dirname($this->file);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755)) {die('Unable to create data directory');}
            chmod($dir, 0755);
            if (!is_file($dir.'/.htaccess')) {
                if (!file_put_contents($dir.'/.htaccess', "Allow from none\nDeny from all\n")) {
                    die('Unable to create .htaccess in data directory');
                }
            }
        }
    }
}
