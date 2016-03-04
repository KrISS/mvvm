<?php

namespace Kriss\Core\ViewModel;

trait RedirectFormViewModelTrait {
    public function success($data) { 
        parent::success($data);
        header("Location: ".'http'.(!empty($_SERVER['HTTPS'])?'s':'').'://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].'/'.$this->model->getSlug());
        exit();
    }
}
