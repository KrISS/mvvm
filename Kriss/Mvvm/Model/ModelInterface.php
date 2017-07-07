<?php

namespace Kriss\Mvvm\Model;

interface ModelInterface {
    public function count($criteria = []);
    public function findBy($criteria = [], $orderBy = null, $offset = null, $limit = null);
    public function findOneBy($criteria = []);
    public function flush();
    public function getData();
    public function getResultClass();
    public function getSlug();
    public function persist($data);
    public function remove($data);
}
