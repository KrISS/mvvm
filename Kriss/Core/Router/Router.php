<?php

namespace Kriss\Core\Router;

use Kriss\Mvvm\Router\RouterInterface;
use Kriss\Mvvm\Request\RequestInterface;

// Inspired from badphp/routes that
class Router implements RouterInterface {
    private $routes = [];
    private $patterns = [];
    private $params = [];
    private $request;

    public function __construct(RequestInterface $request)
    {
        $this->request = $request;
    }

    private function addOneResponse($method, $pattern, $response) {
        $this->routes[] = function ($r_verb, $r_path) use ($method, $pattern, $response) {
            $r_verb = strtoupper(trim($r_verb));
            $r_path = ltrim($r_path, '/');
            // method mismatch

            if ($r_verb !== strtoupper(trim($method))) {
                return null;
            }

            // match requested path against route, while picking out symbols
            $r_pattern = preg_replace(
                [
                    '@<([^:]+)>@U',         // <param> => <param>[^/]+
                    '@<([^:]+)(:(.+))?'.'>@U', // <param:...> => (?<param>...)
                ],
                [
                    '<$1:[^/]+>',
                    '(?<$1>$3)',
                ],
                ltrim($pattern, '/')
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
            $this->addOneResponse($method, implode('', $pattern), $response);
        }
    }

    public function getResponse($method, $uri) {
        foreach($this->routes as $route) {
            if ($match = $route($method, $uri)) { 
                return $this->getMatchedResponse($match);
            }
        }
        
        throw new \Exception($method.':'.$uri.' not found');
    }

    private function getMatchedResponse($match) {
        if (is_callable($match[0])) {
            if (empty($match[1])) return $match[0]();
            else return $this->getCallableMatchedResponse($match);
        }
        return $match[0];
    }

    private function getCallableMatchedResponse($match) {
        // http://www.creapptives.com/post/26272336268/calling-php-functions-with-named-parameters
        $ref = new \ReflectionFunction($match[0]);
        
        $this->params = [];
        foreach( $ref->getParameters() as $p ){
            if (!$p->isOptional() and !isset($match[1][$p->name]))
                throw new \Exception('Missing parameter '.$p->name);
            if (!isset($match[1][$p->name])) $this->params[$p->name] = $p->getDefaultValue();
            else $this->params[$p->name] = $match[1][$p->name];
        }
        return $ref->invokeArgs( $this->params );
    }

    public function getParameters() {
        return $this->params;
    }

    public function generate($name, $params = [], $absolute = false) {
        if (!array_key_exists($name, $this->patterns)) {
            throw new \Exception($name.' not found');
        }

        $pattern = $this->patterns[$name];
        $pattern = preg_replace('@<([^:]+)(:(.+))?>@U', '<$1>', $pattern);

        $query = [];
        foreach($params as $key => $value) {
            $regex = '@\<!?'.$key.'\>@U';
            if (preg_match($regex, $pattern)) {
                $pattern = preg_replace($regex, $value, $pattern);
            } else {
                $query[$key] = $value;
            }
        }

        $pattern = preg_replace('@<!([^:]+)(:(.+))?>@U', '', $pattern);

        if (strpos($pattern, '<') !== false) throw new \Exception('Mandatory parameter '.$pattern);

        $query = http_build_query($query);
        $pattern = $this->request->getBaseUrl() . $pattern . (empty($query)?'':'?'.$query);
        if ($absolute) {
            $pattern = $this->request->getSchemeAndHttpHost() . $pattern;
        }

        return $pattern;
    }
}
