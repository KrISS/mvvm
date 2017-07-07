<?php

namespace Kriss\Core\Router;

use Kriss\Mvvm\Router\RouterInterface;
use Kriss\Mvvm\Request\RequestInterface;

class RequestRouter implements RouterInterface {
    use RouterTrait;

    private $request = null;

    public function __construct(RequestInterface $request) {$this->request = $request;}
    
    public function generate($name, $params = []) {
        return $this->request->getSchemeAndHttpHost()
            . $this->request->getBaseUrl()
            . $this->generateRelativeUrl($name, $params);
    }
}
