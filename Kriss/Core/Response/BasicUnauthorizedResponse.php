<?php

namespace Kriss\Core\Response;

use Kriss\Mvvm\Response\ResponseInterface;

use Kriss\Mvvm\Request\RequestInterface;
use Kriss\Mvvm\Session\SessionInterface;

class BasicUnauthorizedResponse implements ResponseInterface {
    private $request;
    private $session;
    
    use ResponseTrait;

    public function __construct(SessionInterface $session, RequestInterface $request) {
        $this->request = $request;
        $this->session = $session;
    }
    
    public function send() {
        $realm = "KrISS".(empty($this->session->get('secret', ''))?'':':'.$this->session->get('secret', ''));
        $this->sendHeadersBody([
            [$this->request->getServer('SERVER_PROTOCOL', 'HTTP/1.0').' 401 Unauthorized'],
            ['WWW-Authenticate', 'Basic realm="'.$realm.'"'],
        ], 'Not authorized');
    }
}
