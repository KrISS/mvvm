<?php

namespace Kriss\Core\Response;

use Kriss\Mvvm\Response\ResponseInterface;

class UnauthorizedResponse implements ResponseInterface {
    use ResponseTrait;

    public function send() {$this->sendHeadersBody([[$_SERVER['SERVER_PROTOCOL'].' 401 Unauthorized']], 'Not authorized');}
}
