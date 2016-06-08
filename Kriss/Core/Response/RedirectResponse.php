<?php

namespace Kriss\Core\Response;

use Kriss\Mvvm\Response\ResponseInterface;

class RedirectResponse extends Response {
    protected $headers = [];
    protected $body;
    
    public function __construct($url = '')
    {
        $this->headers = [
            ['Location', $url]
        ];
    }
}