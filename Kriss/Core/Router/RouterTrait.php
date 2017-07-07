<?php
  
namespace Kriss\Core\Router;

trait RouterTrait {
    private $matches = [];
    private $methods = [];
    private $responses = [];
    private $patterns = [];
    private $routeParameters = null;
    
    private function getMatchedRoute($match) {
        if (is_callable($match[0])) {
            return call_user_func_array($match[0], $match[1]);
        }
        return $match[0];
    }
    
    public function getRouteParameters() {
        if (is_null($this->routeParameters)) throw new \Exception('Route not dispatched');
        return $this->routeParameters;
    }
    
    public function dispatch($method, $pathInfo) {
        $allowedMethods = [];
        foreach($this->matches as $name => $routes) {
            foreach($routes as $route) {
                if ($match = $route($pathInfo)) {
                    if (in_array($method, $this->methods[$name])) {
                        $this->routeParameters = $match[1];
                        return $this->getMatchedRoute($match);
                    } else {
                        $allowedMethods = array_merge($allowedMethods, $this->methods[$name]);
                    }
                }
            }
        }

        if ($allowedMethods) {
            throw new \Exception($method.': not allowed: '.implode(',', $allowedMethods));
        }

        throw new \Exception($method.': '.$pathInfo.' not found');
    }
    
    private function extractRoutePattern($pattern) {
        return preg_replace(
            [
                '@<([^:]+)>@U',         // <param> => <param>[^/]+
                '@<([^:]+)(:(.+))?>@U', // <param:...> => (?<param>...)
            ],
            [
                '<$1:[^/]+>',
                '(?<$1>$3)',
            ],
            ltrim($pattern, '/')
        );
    }
    
    // inspired from dead project ? https://github.com/badphp/routes <?php
    private function addOneRoute($name, $pattern, $response) {
        $pattern = $this->extractRoutePattern($pattern);
        $this->matches[$name][] = function ($routePath) use ($pattern, $response) {
            $routePath = ltrim($routePath, '/');
            // match requested path against route
            if (!preg_match('@^'.$pattern.'$@', $routePath, $vals)) {
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

    public function getRoutes($name = null) {
        if (!is_null($name)) return [$this->methods[$name], $this->patterns[$name], $this->responses[$name]];
        $results = [];
        foreach(array_keys($this->matches) as $name) {
            $results[$name] = $this->getRoutes($name);
        }

        return $results;
    }
        
    public function setRoute($name, $methods, $pattern, $response) {
        if (is_string($methods)) $methods = [$methods];
        $this->methods[$name] = array_map('strtoupper', $methods);
        $this->patterns[$name] = $pattern;
        $this->responses[$name] = $response;
        $this->matches[$name] = [];
        
        $optionalRoutes = explode('!', str_replace('<!', '!<', $pattern));
        $pattern = [];
        foreach($optionalRoutes as $oneRoute) {
            $pattern[] = $oneRoute;
            $this->addOneRoute($name, implode('', $pattern), $response);
        } 
    }
      
    private function generateRelativeUrl($name, $params = []) {
        if (!array_key_exists($name, $this->patterns)) {
            throw new \Exception($name.' not found');
        }
        
        $pattern = $this->patterns[$name];
        $pattern = preg_replace('@<([^:]+)(:(.+))?>@U', '<$1>', $pattern);
      
        $query = [];
        foreach($params as $key => $value) {
            $regex = '@\<!?'.$key.'\>@U';
            if (preg_match($regex, $pattern)) {
                preg_match('@\<!?'.$key.':(.+)\>@U', $this->patterns[$name], $format);
                if (empty($format) || preg_match('@^'.$format[1].'$@', $value)) {
                    $pattern = preg_replace($regex, $value, $pattern);
                } else {
                    throw new \Exception('Invalid parameter '.$key.': "'.$value.'" does not match format '.$format[1]);
                }
            } else {
                $query[$key] = $value;
            }
        }
  
        $pattern = preg_replace('@<!([^:]+)(:(.+))?>@U', '', $pattern);

        if (strpos($pattern, '<') !== false) throw new \Exception('Mandatory parameter '.$pattern);
        $query = http_build_query($query);
        $pattern = $pattern . (empty($query)?'':'?'.$query);
                          
        return $pattern;
    }
}
