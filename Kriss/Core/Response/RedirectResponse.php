<?php

namespace Kriss\Core\Response;

use Kriss\Mvvm\Response\ResponseInterface;

class RedirectResponse implements ResponseInterface {
    use ResponseTrait;

    private $uri;

    public function __construct($uri = '') {
        if (is_callable($uri)) { $uri = $uri(); }
        $this->uri = $uri;
    }

    public function send() {
        $this->sendHeadersBody([['Location', $this->uri]]);
    }
}
