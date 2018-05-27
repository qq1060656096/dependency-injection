<?php
include_once 'vendor/autoload.php';
class A{
    private $b = null;
    public function __construct(\B $b, $a, $name='11')
    {
        $this->b = $b;

    }

    public function test(){
        echo self::class;
        $this->b->test();
    }
}
class B{
    private $c = null;
    public function __construct(C $c)
    {
        $this->c = $c;
    }

    public function test(){
        echo self::class;
        $this->c->test();
    }
}

class C{
    public function test(){
        echo self::class;
    }
}

$container = new \Zwei\DependencyInjection\Container();
$result = $container->get(A::class);

$result->test();
