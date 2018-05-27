<?php
namespace Zwei\DependencyInjection\Tests;


use Zwei\DependencyInjection\Container;
use Zwei\DependencyInjection\Tests\stubs\Bar;
use Zwei\DependencyInjection\Tests\stubs\BarProperty;
use Zwei\DependencyInjection\Tests\stubs\Foo;

/**
 * 容器测试
 *
 * Class ContainerTest
 * @package Zwei\DependencyInjection\Tests
 */
class ContainerTest extends DependencyInjectionTestCase
{

    /**
     * 测试容器 set get
     */
    public function testSetGet()
    {
        $Bar = new Bar();
        $container = new Container();

        // 使用对象定义
        $container->set('Bar', $Bar);
        $resultBar = $container->get('Bar');
        $this->assertInstanceOf(Bar::class, $resultBar);
        $definitions = $container->getDefinitions();
        $this->assertInstanceOf(Bar::class, $definitions['Bar']);

        // 使用类名定义
        $container->set(Bar::class, Bar::class);
        $resultBar2 = $container->get(Bar::class);
        $this->assertInstanceOf(Bar::class, $resultBar2);

        // 使用方法定义
        $container->set('BarFun', function(){
           return 'Bar Function';
        });
        $BarFun = $container->get('BarFun');
        $this->assertEquals('Bar Function', $BarFun);


        // 使用数组定义
        // 注意Foo::__construct(Bar $bar, $params)类构造方法0个参数是Bar类的实例
        // 因为我们要通过容器依赖加载就不能设置0个参数
        // 我们设置$params参数就是第1个,所以这里索引是1
        $fooDefinition = [
            'class' => Foo::class,
        ];
        $fooParams = [
            '1' => 'foo-test-params',
        ];
        $container->set('foo', $fooDefinition, $fooParams);
        /* @var Foo $resultFoo */
        $resultFoo = $container->get('foo');
        $this->assertInstanceOf(Bar::class, $resultFoo->bar);
        $this->assertEquals('foo-test-params', $resultFoo->pararms);

        // 类定义并设置输
        $barProperty = [
            'name' => 'andy',
            'age' => 18,
        ];
        $container->set('BarProperty', BarProperty::class);
        /* @var BarProperty $resultBarProperTy*/
        $resultBarProperTy = $container->get('BarProperty', [], $barProperty);
        $this->assertEquals('andy', $resultBarProperTy->name);
        $this->assertEquals(18, $resultBarProperTy->age);

    }
}