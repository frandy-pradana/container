<?php

namespace Flash\Container;

use ArrayAccess;
use ReflectionClass;
use Flash\Container\IsNotInstantiableException;

class Container implements ArrayAccess
{
    protected $bindings = [];
    
    protected $instances = [];
    
    public function bind($id,$value,$singleton = false)
    {
        $this->bindings[$id] = compact('value','singleton');
    }
    
    public function singleton($key,$value)
    {
        $this->bind($key,$value,true);
    }
    
    public function get($id)
    {
        if(!array_key_exists($id,$this->bindings)){
            return null;
        }
        
        return $this->bindings[$id];
    }
    
    public function singletonResolved($key)
    {
        return array_key_exists($key,$this->instances);
    }
    
    public function isSingleton($key)
    {
        $binding = $this->get($key);
        
        if(is_null($binding)){
            return false;
        }
        return $binging['singleton'];
    }
    
    public function getSingletonInstance($key)
    {
        return $this->singletonResolved($key) ? $this->instances[$key] : null;
    }
    
    public function resolve($key,array $args = [])
    {
        $class = $this->get($key);
        
        if(is_null($class)){
            $class = $key;
        }
        
        if($this->isSingleton($key) && $this->singletonResolved($key)){
            return $this->getSingletonInstance($key);
        }
        
        $object = $this->build($class,$args);
        return $this->buildObject($object);
    }
    
    public function buildObject($obj)
    {
        if($this->isSingleton[$key]){
            $this->instances[$key] = $obj;
        }
        
        return $obj;
    }
    
    protected function build($class,array $args = [])
    {
        $className = $class['value'];
        $reflector = new ReflectionClass($className);
        
        if(!$reflector->isInstantiable()){
            throw new IsNotInstantiableException("class {$className} is not resolvable dependencies");
        }
        
        if($reflector->getConstructor() !== null){
            $constructor = $reflector->getConstructor();
            $dependencies = $constructor->getParameters();
            $args = $this->buildDependencies($args,$dependencies,$class);
        }
        $obj = $reflector->newInstanceArgs($args);
        return $obj;
    }
    
    protected function buildDependencies($args,$dependencies,$class)
    {
        foreach($dependencies as $dependency){
            if($dependency->isOptional()) continue;
                
                if($dependency->isArray()) continue;
                
                $class = $dependency->getClass();
                
                if(is_null($class)) continue;
                
                if(get_class($this) === $class->name){
                    array_unshift($args,$this);
                    continue;
                }
                
                array_unshift($args,$this->resolve($class->name));
            }
        return $args;
    }
    
    public function offsetGet($key)
    {
        return $this->resolve($key);
    }
    
    public function offsetSet($key,$value)
    {
        $this->bind($key,$value);
    }
    
    public function offsetExists($key)
    {
        return array_key_exists($key,$this->bindings);
    }
    
    public function offsetUnset($key)
    {
        unset($this->bimdings[$key]);
    }

}