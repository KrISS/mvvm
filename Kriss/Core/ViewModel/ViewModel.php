<?php

namespace Kriss\Core\ViewModel;

use Kriss\Mvvm\ViewModel\ViewModelInterface;
use Kriss\Mvvm\Model\ModelInterface;

class ViewModel implements ViewModelInterface {
    use ViewModelTrait;
    
    public function __construct(ModelInterface $model) {$this->model = $model;}

    public function getData() {
        $data = [];
        $pagination = ['current' => ((int)$this->offset)/$this->limit+1, 'total' => (int)(count($this->model->findBy($this->criteria))/$this->limit)];
        if ($pagination['total'] > 0) $data['pagination'] = $pagination;
        $data['slug'] = $this->model->getSlug();
        $data['data'] = $this->model->findBy($this->criteria, $this->limit, $this->offset, $this->orderBy);
        return $data;
    }
}
