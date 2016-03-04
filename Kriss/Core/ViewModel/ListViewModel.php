<?php

namespace Kriss\Core\ViewModel;

use Kriss\Mvvm\ViewModel\ListViewModelInterface;
use Kriss\Mvvm\Model\ListModelInterface as Model;

class ListViewModel implements ListViewModelInterface {
    protected $model;
    protected $id;

    public function __construct(Model $model) {
        $this->model = $model;
        $this->id = null;
    }

    public function getData() {
        return [$this->model->getSlug() => is_null($this->id)?$this->model->findBy():$this->model->findOneBy($this->id)];
    }

    public function setId($id) {
        $this->id = $id;
    }
}
