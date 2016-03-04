<?php

namespace Kriss\Demo;

use Kriss\Mvvm\Response\ResponseInterface;

class Response implements ResponseInterface {
    protected $body;
    protected $headers;

    public function __construct($body = '', $headers = [])
    {
        $this->body = $body;
        $this->headers = $headers;
    }

    public function send() {
        foreach ($this->headers as $header) {
            header($header);
        }
        echo $this->body;
    }
}
