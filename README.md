# dependency-injection

## 1. 依赖倒置

> 程序要依赖于抽象接口，不要依赖于具体实现。简单的说就是要求对抽象进行编程，不要对实现进行编程，这样就降低了客户与实现模块间的耦合。

> 为了降低模块与模块之间的"耦合度"，提倡模块与模块之间不要直接发生依赖关系。
> 依赖倒置是一种软件设计思想，在传统软件中，上层代码依赖于下层代码，当下层代码有所改动时，上层代码也要相应进行改动，因此维护成本较高。
> 而依赖倒置原则的思想是，上层不应该依赖下层，应依赖接口。意为上层代码定义接口，下层代码实现该接口，从而使得下层依赖于上层接口，降低耦合度，提高系统弹性


> 1. 上层模块不应该依赖下层模块，他们都应该依赖于抽象。
> 2. 抽象不应该依赖于具体实现，具体实现应该依赖于抽象。

## 2. 什么是依赖注入
> 通俗讲, 依赖注入是通过外部传递依赖的方式。为了软件解耦，也方便做单元测试

## 3. 什么是控制反转
> 平时我们在创建对象的时候控制权在我们手中，现在我们控制转移到第三方。借助第三方实现具有依赖关系的的对象之间的解耦，这个第三方就是容器(container)
    
> 我们也可以使用工厂模式解决这个问题，但是每次都得去改工厂类的方法，因为控制反转实现了依赖注入、依赖查找、服务定位。

使用说明
> 更过demo请看"tests/ContainerTest.php"
```php
<?php
include_once 'vendor/autoload.php';
class A{
    private $b = null;
    public function __construct(\B $b)
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
$result->test();// 输出ABC

```

> 上层使用者,
# 单元测试使用
  
> --bootstrap 在测试前先运行一个 "bootstrap" PHP 文件
* **--bootstrap引导测试:** phpunit --bootstrap ./Tests/TestInit.php ./Tests/
D:\phpStudy\php\php-7.0.12-nts\php.exe D:\phpStudy\php\php-5.6.27-nts\composer.phar update
  
D:\phpStudy\php\php-7.0.12-nts\php.exe vendor\phpunit\phpunit\phpunit --bootstrap tests/TestInit.php tests/