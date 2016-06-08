<?php

namespace Kriss\Core\Response;

use Kriss\Mvvm\Response\ResponseInterface;

class Response implements ResponseInterface {
    protected $headers;
    protected $body;
    
    public function __construct($body = '', $headers = [])
    {
        $this->body = $body;
        $this->headers = $headers;
    }

    public function send() {
        foreach ($this->headers as $header) {
            header($header[0] . ': ' . $header[1]);
        }
        echo $this->body;
    }
}