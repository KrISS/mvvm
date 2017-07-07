<?php

namespace Kriss\Core\FormAction;

use Kriss\Mvvm\Form\FormInterface;
use Kriss\Mvvm\Model\ModelInterface;
use Kriss\Mvvm\Request\RequestInterface;

trait FormActionTrait  {
    private $model;
    private $form;
    private $request;
    private $resetData;
    private $flush = false;
    
    public function __construct(ModelInterface $model, FormInterface $form, RequestInterface $request, $resetData = false) {
        $this->model = $model;
        $this->form = $form;
        $this->request = $request;
        $this->resetData = $resetData;
    }
    
    public function traitSuccess($data) {
        if ($this->resetData) $this->model->remove();
        $this->form->setFormData($data);
        $data = $this->form->getData();

        if (is_array($data)) {
            // associative array
            if (empty(array_filter(array_keys($data), 'is_int'))) $this->saveEntity($data);
            else foreach($data as $entity) $this->saveEntity($entity);
        } else {
            $this->saveEntity($data);
        }
        if ($this->flush) $this->model->flush();
        header('Location: '.$this->request->getSchemeAndHttpHost().$this->request->getBaseUrl());
    }
    
    public function traitFailure($data) {
        if (array_key_exists('_', $data)) $data = $data['_'];
        $this->form->setData($data);
    }
}
