<?php

namespace Kriss\Core\Container;

use Kriss\Mvvm\Container\ContainerInterface;

class Container implements ContainerInterface {
    protected $container = [];
    protected $rules = [];

    public function has($id) {return class_exists($id)||isset($this->container[$this->getId($id)]);}
    
    public function get($id, $args = []) {
        if ($this->has($this->getId($id).'_instance')) {
            return $this->get($this->getId($id).'_instance');
        } else if (isset($this->container[$this->getId($id)])) {
            $get = $this->container[$this->getId($id)];
            $instance = is_callable($get)?$get($this, $args):$get;
            $this->setInstance($id, $instance);
            return $instance;
        } else if (class_exists($id)) {
            $instance = new $id($args);
            $this->setInstance($id, $instance);
            return $instance;
        } else {
            throw new \Exception($id.' not found');
        }
    }

    public function getRule($id) {return $this->rules[$id];}

    public function set($id, $rules = array()) {
        $this->rules[$id] = $rules;
        if (!$this->has($this->getId($id).'_class')) $this->container[$this->getId($id).'_class'] = $id;
        $id = $this->getId($id);
        foreach ($rules as $key => $rule) {
            switch($key) {
            case 'instanceOf':
                $this->container[$id.'_class'] = $rule;
                break;
            case 'constructParams':
                $this->container[$id.'_params'] = $rule;
                break;
            case 'call':
                $this->container[$id.'_call'] = $rule;
                break;
            case 'shared':
                $this->container[$id.'_shared'] = $rule;
                break;
            }
        }
        
        $this->container[$id] = function ($container, $params = []) use ($id) {
            $class = new \ReflectionClass($container->has($id.'_class') ? $container->get($id.'_class') : $id);
            if ($container->has($id.'_params')) {
                $params = [];
                foreach($container->get($id.'_params') as $param) {
                    if (is_array($param) && isset($param['instance'])) {
                        $params[] = $container->get($param['instance']);
                    } else {
                        $params[] = $param;
                    }
                }
            }
            $instance = $class->newInstanceArgs($params);

            if ($container->has($id.'_call')) {
                foreach($container->get($id.'_call') as $call) {
                    call_user_func_array(array($instance, $call[0]), $call[1]);
                }
            }

            return $instance;
        };
    }

    private function getId($id) {return ltrim(strtolower($id), '\\');}

    private function setInstance($id, $instance) {
        if (isset($this->container[$this->getId($id).'_shared'])
        && $this->container[$this->getId($id).'_shared']) {
            $this->container[$this->getId($id).'_instance'] = $instance;
        }
    }
}
