<?php

namespace Kriss\Core\Request;

use Kriss\Mvvm\Request\RequestInterface;

class Request implements RequestInterface {
    private $basePath = '';
    private $baseUrl = '';
    private $host = 'localhost';
    private $method = 'GET';
    private $pathInfo = '';
    private $port = 80;
    private $queryString = '';
    private $requestUri = ''; // pathInfo and queryString
    private $scheme = 'http';

    public function __construct($uri = null, $method = 'GET') {
        if (is_null($uri)) {
            $this->initFromServer();
        } else {
            $this->initFromUri($uri, $method);
        }
    }

    public function getBaseUrl() {return $this->baseUrl;}

    public function getHost() {return $this->host;}

    public function getPathInfo() {return $this->pathInfo;}

    public function getPort() {return $this->port;}

    public function getQueryString() {return $this->queryString;}

    public function getScheme() {return $this->scheme;}

    public function getMethod() {return $this->method;}

    public function getRequest($request = null, $default = null) {
        if (is_null($request)) return $_POST;
        return isset($_POST[$request])?$_POST[$request]:$default;
    }

    public function getQuery($query = null, $default = null) {
        if (is_null($query)) return $_GET;
        return isset($_GET[$query])?$_GET[$query]:$default;
    }

    public function getRequestUri() {return $this->requestUri;}

    public function setBaseUrl($baseUrl) {$this->baseUrl = rtrim($baseUrl, '/');}

    public function setPathInfo($pathInfo) {$this->pathInfo = $pathInfo;}

    public function setMethod($method) {$this->method = strtoupper($method);}

    public function setHost($host) {$this->host = preg_replace('/:(.*)$/i', "", strtolower($host));}
    
    public function setRequestUri($requestUri) {$this->requestUri = $requestUri;}

    public function setScheme($scheme) {$this->scheme = strtolower($scheme);}

    public function setPort($port) {$this->port = (int) $port;}
    // string cast to be fault-tolerant, accepting null
    public function setQueryString($queryString) {$this->queryString = (string) $queryString;}

    public function getUri() {
        $queryString = $this->getQueryString();
        $queryString = empty($queryString)?'':'?'.$queryString;
        return $this->getSchemeAndHttpHost().$this->getBaseUrl().$this->getPathInfo().$queryString;
    }

    public function getSchemeAndHttpHost() {
        $scheme = $this->getScheme();
        $port = $this->getPort();
        $defaultPort = $scheme.$port === 'http80' || $scheme.$port === 'https443';

        return $scheme.'://'.$this->getHost().(($defaultPort)?'':':'.$port);
    }

    private function prepareRequestUri() {
        $requestUri = '';
        if (!empty($this->getServer('REQUEST_URI'))) {
            $requestUri = $this->getServer('REQUEST_URI');
            // HTTP proxy reqs setup request URI with scheme and host [and port] + the URL path, only use URL path
            $schemeAndHttpHost = $this->getSchemeAndHttpHost();
            if (strpos($requestUri, $schemeAndHttpHost) === 0) {
                $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
            }
        } elseif (!empty($this->getServer('ORIG_PATH_INFO'))) {
            // IIS 5.0, PHP as CGI
            $requestUri = $this->getServer('ORIG_PATH_INFO');
            if ('' != $this->getQueryString()) {
                $requestUri .= '?'.$this->getQueryString();
            }
        }
 
        return $requestUri;
    }

    // https://github.com/zendframework/zend-http/blob/master/src/PhpEnvironment/Request.php
    private function prepareBaseUrl() {
        $filename       = basename($this->getServer('SCRIPT_FILENAME', ''));
        $scriptName     = $this->getServer('SCRIPT_NAME');
        $phpSelf        = $this->getServer('PHP_SELF');
        $origScriptName = $this->getServer('ORIG_SCRIPT_NAME');

        if (basename($scriptName) === $filename) {
            $baseUrl = $scriptName;
        } elseif (basename($phpSelf) === $filename) {
            $baseUrl = $phpSelf;
        } elseif (basename($origScriptName) === $filename) {
            $baseUrl = $origScriptName; // 1and1 ?
        } else {
            // Backtrack up the SCRIPT_FILENAME to find the portion
            // matching PHP_SELF.
            $baseUrl  = '/';
            $basename = $filename;
            if ($basename) {
                $path     = ($phpSelf ? trim($phpSelf, '/') : '');
                $basePos  = strpos($path, $basename) ?: 0;
                $baseUrl .= substr($path, 0, $basePos) . $basename;
            }
        }

        $requestUri = $this->getRequestUri();

        // Full base URL matches.
        if (0 === strpos($requestUri, $baseUrl)) {
            return $baseUrl;
        }

        // Directory portion of base path matches.
        $baseDir = str_replace('\\', '/', dirname($baseUrl));
        if (0 === strpos($requestUri, $baseDir)) {
            return $baseDir;
        }

        $truncatedRequestUri = $requestUri;
        if (false !== ($pos = strpos($requestUri, '?'))) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);
        // No match whatsoever
        if (empty($basename) || false === strpos($truncatedRequestUri, $basename)) {
            $baseUrl = '';
        }

        return $baseUrl;
    }

    private function preparePathInfo() {
        $baseUrl = $this->getBaseUrl();
        $requestUri = $this->getRequestUri();
        if ($pos = strpos($requestUri, '?')) {
            $requestUri = substr($requestUri, 0, $pos);
        }
        $pathInfo = substr($requestUri, strlen($baseUrl));

        return (string) $pathInfo;
    }

    private function initFromServer() {
        $method = $this->getServer('REQUEST_METHOD', 'GET');
        // Header X-HTTP-METHOD-OVERRIDE
        if (isset($_POST['_method']) && $method === 'POST') $method = $_POST['_method'];
        $this->setMethod($method);
        $this->setHost($this->getServer(
            'HTTP_HOST',
            $this->getServer(
                'SERVER_NAME',
                $this->getServer('SERVER_ADDR', $this->host))));

        $isSecure = !empty($this->getServer('HTTPS')) && $this->getServer('HTTPS') == 'on';
        $this->setScheme('http'.($isSecure?'s':''));
        $this->setPort($this->getServer('SERVER_PORT', ($isSecure?'443':'80')));
        $this->setQueryString($this->getServer('QUERY_STRING'));
        $this->setRequestUri($this->prepareRequestUri());
        $this->setBaseUrl($this->prepareBaseUrl());
        $this->setPathInfo($this->preparePathInfo());
    }

    private function initFromUri($uri, $method) {
        $this->setMethod($method);

        $components = parse_url($uri);

        if (isset($components['host'])) {
            $this->setHost($components['host']);
        }
        
        if (isset($components['scheme'])) {
            $this->setScheme($components['scheme']);
            if ('https' === $components['scheme']) {
                $this->setPort(443);
            }
        }

        if (isset($components['port'])) {
            $this->setPort($components['port']);
        }

        if (isset($components['query'])) {
            $this->setQueryString($components['query']);
        }
        
        if (isset($components['path'])) {
            $queryString = $this->getQueryString();
            $this->setRequestUri($components['path'].('' !== $queryString ? '?'.$queryString : ''));
        }

        $this->setBaseUrl($this->prepareBaseUrl());
        $this->setPathInfo($this->preparePathInfo());

    }

    public function getServer($server = null, $default = null) {
        if (is_null($server)) return $_SERVER;
        return isset($_SERVER[$server])?$_SERVER[$server]:$default;
    }
}
