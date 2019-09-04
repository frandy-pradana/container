<?php

namespace Flash\Container;

use ArrayAccess;
use ReflectionClass;
use Flash\Container\NotFoundException;
use Psr\Container\ContainerInterface;
use Flash\Container\IsNotInstantiableException;

class Container implements ArrayAccess,ContainerInterface
{
    /**
    * @var array
    */
    protected $bindings = [];
    
    /**
    * @var array
    */
    protected $instances = [];
    
    /**
    * Make & create new indentifier
    * 
    * @param string $id
    * @param string $value
    * @param bool $singleton
    * return void
    */
    public function make($id,$value,$singleton = false)
    {
        $this->bindings[$id] = compact('value','singleton');
    }
    
    /**
    * Create new singleton
    * 
    * @param string $key
    * @param string $value
    * @return void
    */
    public function singleton($key,$value)
    {
        $this->make($key,$value,true);
    }
    
    /**
    * {@inherit}
    */
    public function get($id)
    {
        if(!$this->has($id)){
            throw new NotFoundException('No entry was found to ['.$id.'] identifier');
        }
        
        return $this->bindings[$id];
    }
    
    /**
    * Get the resolve singleton
    * 
    * @param string $key
    * @return bool
    */
    public function singletonResolved($key)
    {
        return array_key_exists($key,$this->instances);
    }
    
    /**
    * Get generete is singleton
    * 
    * @param string $key
    */
    public function isSingleton($key)
    {
        $binding = $this->get($key);
        
        if(is_null($binding)){
            return false;
        }
        return $binging['singleton'];
    }
    
    /**
    * {@Inherit}
    */
    public function has($id)
    {
        return (array_key_exists($id,$this->bindings)) ? true : false;
    }
    
    /**
    * Get the singleton instance
    * 
    * @param string $key
    * @return null|object
    */
    public function getSingletonInstance($key)
    {
        return $this->singletonResolved($key) ? $this->instances[$key] : null;
    }
    
    /**
    * Get the access instance class
    * 
    * @param string $key | name class
    * @param array $args
    * @return object
    */
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
    
    /**
    * Prepare object the instance
    * 
    * @param string $obj
    * @return object
    */
    public function buildObject($obj)
    {
        if($this->isSingleton[$key]){
            $this->instances[$key] = $obj;
        }
        
        return $obj;
    }
    
    /**
    * Get bild instance  class bindings
    * 
    * @param array $class
    * @param array $args
    * @throw \Flash\Container\IsNotInstantiableException
    * @return array
    */
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
    
    /**
    * Get the prepare dependencies
    * 
    * @param array $args
    * @param array $dependencies
    * @param mixed $class
    * return array
    */
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
    
    /*{@Inherit}*/
    public function offsetGet($key)
    {
        return $this->resolve($key);
    }
    
    /*{@Inherit}*/
    public function offsetSet($key,$value)
    {
        $this->make($key,$value);
    }
    
    /*{@Inherit}*/
    public function offsetExists($key)
    {
        return $this->has($key);
    }
    
    /*{@Inherit}*/
    public function offsetUnset($key)
    {
        unset($this->bimdings[$key]);
    }

}