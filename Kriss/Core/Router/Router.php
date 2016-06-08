<?php

namespace Kriss\Core\Router;

use Kriss\Mvvm\Router\RouterInterface;

class Router implements RouterInterface {
    private $routes = [];
    private $patterns = [];
    private $params = [];

    private function addOneResponse($method, $pattern, $response) {
        $this->routes[] = function ($r_verb, $r_path) use ($method, $pattern, $response) {
            $r_verb = strtoupper(trim($r_verb));
            $r_path = trim($r_path, '/');
            // method mismatch

            if ($r_verb !== strtoupper(trim($method))) {
                return null;
            }

            // match requested path against route, while picking out symbols
            $r_pattern = preg_replace(
                [
                    '@<([^:]+)>@U',         // <param> => <param>[^/]+
                    '@<([^:]+)(:(.+))?>@U', // <param:...> => (?<param>...)
                ],
                [
                    '<$1:[^/]+>',
                    '(?<$1>$3)',
                ],
                trim($pattern, '/')
            );

            // mismatch
            if (!preg_match('@^'.$r_pattern.'$@', $r_path, $vals)) {
                return null;
            }

            // process matched route symbols
            $vals = array_map('urldecode', array_intersect_key(
                array_slice($vals, 1),
                array_flip(array_filter(array_keys($vals), 'is_string'))
            ));
            // return matching route + route symbols
            return [$response, $vals];
        };
    }

    public function addResponse($name, $method, $pattern, $response) {
        $this->patterns[$name] = $pattern;

        $optionalRoutes = explode('!', str_replace('<!', '!<', $pattern));
        $pattern = [];
        foreach($optionalRoutes as $oneRoute) {
            $pattern[] = $oneRoute;
            $this->addOneResponse($method, implode('',$pattern), $response);
        }
    }

    public function getResponse($method, $uri) {
        foreach($this->routes as $route) {
            if ($match = $route($method, $uri)) { 
                if (is_callable($match[0])) {
                    if (empty($match[1])) return $match[0]();

                    // http://www.creapptives.com/post/26272336268/calling-php-functions-with-named-parameters
                    $ref = new \ReflectionFunction($match[0]);
                    
                    $this->params = [];
                    foreach( $ref->getParameters() as $p ){
                        if (!$p->isOptional() and !isset($match[1][$p->name])) throw new \Exception("Missing parameter $p->name");
                        if (!isset($match[1][$p->name])) $this->params[$p->name] = $p->getDefaultValue();
                        else $this->params[$p->name] = $match[1][$p->name];
                    }
                    return $ref->invokeArgs( $this->params );
                }

                return $match[0];
            }
        }

        throw new \Exception($method.':'.$uri.' not found');
    }

    public function getParameters() {
        return $this->params;
    }

    public function generate($name, $params = [], $absolute = false) {
        $pattern = $this->patterns[$name];
        $pattern = preg_replace('@<([^:]+)(:(.+))?>@U', '<$1>', $pattern);

        $query = [];
        foreach($params as $key => $value) {
            if (strpos($pattern, '<'.$key.'>') === false && !is_null($value)) {
                $query[] = $key.'='.$value;
            } else {
                $pattern = str_replace('<'.$key.'>', $value, $pattern);
            }
        }

        if (!empty($query)) {
            $pattern .= '?'.implode($query, '&');
        }

        $baseUrl = $this->getBaseUrl();
        $pattern = $baseUrl . $pattern;

        if ($absolute) {
            $schemeHttpHost = $this->getSchemeAndHttpHost();
            $pattern = $schemeHttpHost . $pattern;
        }

        return $pattern;
    }

    private function getBaseUrl()
    {
        $filename = basename($_SERVER['SCRIPT_FILENAME']);
        if (basename($_SERVER['SCRIPT_NAME']) === $filename) {
            $baseUrl = $_SERVER['SCRIPT_NAME'];
        } elseif (basename($_SERVER['PHP_SELF']) === $filename) {
            $baseUrl = $_SERVER['PHP_SELF'];
        } elseif (basename($_SERVER['ORIG_SCRIPT_NAME']) === $filename) {
            $baseUrl = $_SERVER['ORIG_SCRIPT_NAME'];
        } else {
            // Backtrack up the script_filename to find the portion matching
            // php_self
            $path = $_SERVER['PHP_SELF'];
            $file = $_SERVER['SCRIPT_FILENAME'];
            $segs = explode('/', trim($file, '/'));
            $segs = array_reverse($segs);
            $index = 0;
            $last = count($segs);
            $baseUrl = '';
            do {
                $seg = $segs[$index];
                $baseUrl = '/'.$seg.$baseUrl;
                ++$index;
            } while ($last > $index && (false !== $pos = strpos($path, $baseUrl)) && 0 != $pos);
        }

        $requestUri = $_SERVER['REQUEST_URI'];
        $schemeAndHttpHost = $this->getSchemeAndHttpHost();
        if (strpos($requestUri, $schemeAndHttpHost) === 0) {
            $requestUri = substr($requestUri, strlen($schemeAndHttpHost));
        }

        if ($baseUrl && false !== $prefix = $this->getPrefix($requestUri, $baseUrl)) {
            // full $baseUrl matches
            return rtrim($prefix, '/');
        }

        if ($baseUrl && false !== $prefix = $this->getPrefix($requestUri, rtrim(dirname($baseUrl), '/'))) {
            // directory portion of $baseUrl matches
            return rtrim($prefix, '/');
        } 

        if (strlen($requestUri) >= strlen($baseUrl) && (false !== $pos = strpos($requestUri, $baseUrl)) && $pos !== 0) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return rtrim($baseUrl, '/');
    }

    protected function getBasePath()
    {
        $filename = basename($this->getServer('SCRIPT_FILENAME'));
        $baseUrl = $this->getBaseUrl();
        if (empty($baseUrl)) {
            return '';
        }

        if (basename($baseUrl) === $filename) {
            $basePath = dirname($baseUrl);
        } else {
            $basePath = $baseUrl;
        }

        return rtrim($basePath, '/');
    }

    // All private functions will be moved to a RequestContext object
    private function getScheme()
    {
        return 'http'.((strtolower($this->getServer('HTTPS')) == 'on' || $this->getServer('SERVER_PORT') == '443')?'s':'');
    }

    private function getPort()
    {
        return $this->getServer('SERVER_PORT');
    }

    private function getHost()
    {
        return $this->getServer('SERVER_NAME', $this->getServer('SERVER_ADDR'));
    }

    private function getSchemeAndHttpHost()
    {
        $scheme = $this->getScheme();
        $port = $this->getPort();
        $schemeHost = $scheme.'://'.$this->getHost();

        return (('http' == $scheme && $port == 80) || ('https' == $scheme && $port == 443))
               ?$schemeHost:$schemeHost.':'.$port;
    }

    private function getPrefix($string, $prefix)
    {
        if (0 !== $pos = strpos($string, $prefix)) {
            return false;
        }

        return substr($string, 0, strlen($prefix));
    }

    private function getServer($index, $default = '')
    {
        return isset($_SERVER[$index])?$_SERVER[$index]:$default;
    }
}
