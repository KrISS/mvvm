<?php

namespace Kriss\Core\Response;

use Kriss\Mvvm\Response\ResponseInterface;

class Response implements ResponseInterface {
    protected $headers = [];
    protected $body;
    
    public function __construct($body = '')
    {
        $this->body = $body;
    }

    public function send() {
        foreach ($this->headers as $header) {
            header($header);
        }
        echo $this->body;
    }
}