<?php
namespace Zwei\DependencyInjection;

use ReflectionClass;

/**
 * 标记依赖类,要获取实例
 *
 * Class DependenceClass
 * @package Zwei\ContainerInjection
 */
class DependenceClass
{
    protected $id = null;

    public function __construct(ReflectionClass $id)
    {
        $this->id = $id;
    }
    public function getReflectionClass()
    {
        return $this->id;
    }
}