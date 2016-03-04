<?php

namespace Kriss\Mvvm\Model;

interface ListModelInterface extends ModelInterface {
    public function findOneBy($criteria);
    public function findBy($criteria, $orderBy = null, $limit = null, $offset = null);
}
