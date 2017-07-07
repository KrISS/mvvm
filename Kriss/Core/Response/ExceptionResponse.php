<?php

namespace Kriss\Core\Response;

use Kriss\Mvvm\Response\ResponseInterface;

class ExceptionResponse implements ResponseInterface {
    use ResponseTrait;

    private $exception;
    private $headers;
    
    public function __construct(\Exception $e, $headers = [])
    {
        $this->exception = $e;
        $this->headers = $headers;
    }

    public function send() {$this->sendHeadersBody($this->headers, $this->exception->getMessage());}
}
