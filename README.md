# dependency-injection

## 1. 依赖倒置
> 为了降低模块与模块之间的"耦合度"，提倡模块与模块之间不要直接发生依赖关系。
> 程序要依赖于抽象接口，不要依赖于具体实现。简单的说就是要求对抽象进行编程，不要对实现进行编程，这样就降低了客户与实现模块间的耦合。

> 1. 上层模块不应该依赖下层模块，他们都应该依赖于抽象。
> 2. 抽象不应该依赖于具体实现，具体实现应该依赖于抽象。

## 2. 什么是依赖注入
    通俗讲, 依赖注入是通过传递参数的方式把依赖参数注入到类中,是软件设计的一种思想，为了软件解耦

## 3. 什么是控制反转
    平时我们在创建对象的时候控制权在我们手中，现在我们控制转移到底三方。


> 上层使用者,
# 单元测试使用
  
> --bootstrap 在测试前先运行一个 "bootstrap" PHP 文件
* **--bootstrap引导测试:** phpunit --bootstrap ./Tests/TestInit.php ./Tests/
D:\phpStudy\php\php-7.0.12-nts\php.exe D:\phpStudy\php\php-5.6.27-nts\composer.phar update
  
D:\phpStudy\php\php-7.0.12-nts\php.exe vendor\phpunit\phpunit\phpunit --bootstrap tests/TestInit.php tests/