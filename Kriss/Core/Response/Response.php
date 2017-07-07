<?php

namespace Kriss\Core\Response;

use Kriss\Mvvm\Response\ResponseInterface;

class Response implements ResponseInterface {
    use ResponseTrait;
    
    protected $body = '';
    protected $headers = [];
    
    public function __construct($body = '', $headers = [])
    {
        $this->body = $body;
        $this->headers = $headers;
    }

    public function send() {$this->sendHeadersBody($this->headers, $this->body);}
}
