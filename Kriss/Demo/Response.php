<?php

namespace Kriss\Demo;

use Kriss\Mvvm\Response\ResponseInterface;

class Response implements ResponseInterface {
    protected $body;

    public function __construct($body = '', $headers = [])
    {
        $this->body = $body;
    }

    public function send() {
        echo $this->body;
    }
}
