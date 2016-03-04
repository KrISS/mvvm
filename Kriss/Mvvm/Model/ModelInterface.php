<?php

namespace Kriss\Mvvm\Model;

interface ModelInterface {
    public function getData();
    public function flush();
    public function getResultClass();
    public function getSlug();
    public function persist($data);
    public function remove($data);
}
