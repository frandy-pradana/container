<?php

require_once('vendor/autoload.php');
use Flash\Container\Container;

class Foo {
    public $a = 'halo';
}

class App extends Container
{
    public function __construct()
    {
       // $this->bind('foo','Foo');
        $this['foo'] = "Foo";
    }
    
    public function run()
    {
    
    }

}

$app = new App;

echo '<pre>';
var_dump($app['foo']->a);