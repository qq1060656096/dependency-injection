<?php
namespace Zwei\DependencyInjection\Tests\stubs;


class Foo
{
    public $bar;
    public $pararms;
    public function __construct(Bar $bar, $params)
    {
        $this->bar = $bar;
        $this->pararms = $params;
    }
}