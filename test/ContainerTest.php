<?php

require_once('../vendor/autoload.php');


class ContainerTest extends \PHPUnit\Framework\TestCase
{
    protected $container;
    
    public function setUp():void
    {
         $this->container = new \Flash\Container\Container();
    }
    
   public function test_bindingObjectWork()
    {
        $this->container->make('foo','Bar');
        
        $this->assertEquals('Bar',$this->container->get('foo'));
    }
    
    public function test_returnsNullWhenBindingNotFound()
    {
        $bind = $this->container->get('bar');
        
        $this->assertNull($bind);
    }
    
    public function test_resolveClassReturnsObject()
    {
        $object = $this->container->resolve('Bar');
        
        $this->assertInstanceOf('Bar',$object);
    }
    
    public function test_arrayAccessWorksAsIntended()
    {
        $this->container['qux'] = 'Bar';
        
        $object = $this->container['qux'];
        $this->assertInstanceOf('Bar',$object);
    }
    
}



class Foo {
    
}

class Bar {
    public function __construct(/*Foo $foo*/)
    {
        
    }
}