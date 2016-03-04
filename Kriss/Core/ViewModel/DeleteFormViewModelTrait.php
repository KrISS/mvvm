<?php

namespace Kriss\Core\ViewModel;

trait DeleteFormViewModelTrait {
    public function success($data) { 
        $this->model->remove($data);
        $this->model->flush();
        header("Location: ".$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'/'.$this->model->getSlug());
        exit();
    }
}
