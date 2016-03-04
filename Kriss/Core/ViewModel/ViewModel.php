<?php

namespace Kriss\Core\ViewModel;

use Kriss\Mvvm\ViewModel\ViewModelInterface;
use Kriss\Mvvm\Model\ModelInterface as Model;

class ViewModel implements ViewModelInterface {
    protected $model;

    public function __construct(Model $model) {
        $this->model = $model;
    }

    public function getData() {
        return [$this->model->getSlug() => $this->model->getData()];
    }
}
