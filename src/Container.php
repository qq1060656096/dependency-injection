<?php
namespace Zwei\DependencyInjection;

use Zwei\DependencyInjection\Exception\ClassNotFoundException;
use Zwei\DependencyInjection\Exception\InvalidConfigException;

class Container implements ContainerInterface
{

    /**
     * @var array 存储单列以类名为键
     */
    private $_singletons = [];

    /**
     * @var array 存储各个类的定义  以类的名称为键
     */
    private $_definitions = array();

    /**
     * @var array 存储各个类实例化需要的参数 以类的名称为键
     */
    private $_params = array();


    /**
     * @var array 存储各个类实例化的引用
     */
    private $_reflections = array();

    /**
     * @var array 各个类依赖的类
     */
    private $_dependencies = array();

    /**
     * 容器中设置类的定义
     *
     * @param string $class 类名、接口名、别名
     * @param string|array|callable|object $definition 类型相关定义
     * @param array $params 类构造参数列表
     * @return $this
     */
    public function set($class, $definition = [], array $params = array())
    {

        $this->_definitions[$class] = $this->setNormalizeDefinition($class, $definition);
        $this->_params[$class] = $params;
        if($params){
            $this->_params[$class];
        }
        unset($this->_singletons[$class]);
        return $this;
    }

    /**
     * 容器中设置类的定义,并标记为单列
     *
     * @param string $class 类名、接口名、别名
     * @param string|array|callable|object $definition 类型相关定义
     * @param array $params 类构造参数列表
     * @return $this
     */
    public function setSingleton($class, $definition = [], array $params = [])
    {
        $this->_definitions[$class] = $this->setNormalizeDefinition($class, $definition);
        $this->_params[$class] = $params;
        $this->_singletons[$class] = null;
        return $this;
    }

    /**
     * 设置标准定义
     * @param string $class 类名、接口、别名
     * @param string|array|callable|object $definition 类定义
     * @return array 标准定义
     */
    protected function setNormalizeDefinition($class, $definition)
    {
        switch (true) {
            case empty($definition):
                return ['class' => $class];
                break;
            case is_string($definition):
                return ['class' => $definition];
                break;
            case is_callable($definition, true) || is_object($definition):
                return $definition;
                break;
            case is_array($definition):
                if (!isset($definition['class'])) {
                    if (strpos($class, '\\') === false) {
                        throw new InvalidConfigException('A class definition requires a "class" member.');
                    }
                    $definition['class'] = $class;
                }
                return $definition;
                break;
        }
        throw new InvalidConfigException("Unsupported definition type for \"$class\": " . gettype($definition));
    }

    /**
     * 名称是否在容器中定义
     *
     * @param string $class 类名,接口名,别名
     * @return bool 定义true, 否者false
     * @see set()
     */
    public function has($class)
    {
        return isset($this->_definitions[$class]);
    }


    /**
     * 名称是否定义为单列
     * @param string $class 类名,接口名,别名
     * @return bool
     */
    public function hasSingleton($class)
    {
        return array_key_exists($class, $this->_singletons);
    }

    /**
     * 单列名称是否已经实例化
     *
     * @param string $class 类名,接口名,别名
     * @return bool
     */
    public function hasSingletonInstance($class)
    {
        return isset($this->_singletons[$class]);
    }


    /**
     * 删除指定名称的定义
     *
     * @param $class 类名,接口名,别名
     */
    public function clear($class)
    {
        unset($this->_definitions[$class], $this->_singletons[$class]);
    }

    /**
     * 获取类实例
     *
     * @param string $class 类名
     * @param array $params 参数
     * @param array $properties 实例的属性
     * @return mixed|object
     */
    public function get($class, array $params = array(), array $properties = array())
    {
        // 如果已经设置过单列了,就从单列缓存中取出
        if (isset($this->_singletons[$class])) {
            return $this->_singletons[$class];
        }
        // 没有定义过,直接创建对象
        if (!isset($this->_definitions[$class])) {
            return $this->createObject($class, $params, $properties);
        }
        $definition = $this->_definitions[$class];
        switch (true) {
            case is_callable($definition, true):
                $params = $this->resolveDependencies($this->mergeParams($class, $params));
                $object = call_user_func($definition, $this, $params, $properties);
                break;
            case is_object($definition):
                $object = $definition;
                break;
            case is_array($definition):
                $originClass = $definition['class'];
                $params = $this->mergeParams($class, $params);
                unset($definition[$originClass]);
                if ($originClass === $class) {
                    $object = $this->createObject($class, $params, $properties);
                } else {
                    $object = $this->get($originClass, $params, $properties);
                }
                break;
            default:
                throw new InvalidConfigException(sprintf('Unexpected object definition type: ' . gettype($definition)));
                break;
        }
        // 如果名称在单列中,就把创建好的对象存放到单列中
        if (array_key_exists($class, $this->_singletons)) {
            // singleton
            $this->_singletons[$class] = $object;
        }
        return $object;
    }

    /**
     * 合并参数
     * @param string $class 类名
     * @param array $params 参数
     * @return array 合并后的参数
     */
    protected function mergeParams($class, array $params = array())
    {
        if (empty($this->_params[$class])) {
            return $params;
        } elseif (empty($params)) {
            return $this->_params[$class];
        }

        $oldParams = $this->_params[$class];
        // 新的参数覆盖老的参数
        foreach ($params as $index => $value) {
            $oldParams[$index] = $value;
        }
        return $oldParams;
    }
    /**
     * 创建类实例
     *
     * @param string $class 类名
     * @param array $params 构造方法参数
     * @param array $properties 设置实例的属性
     * @return object
     */
    public function createObject($class, array $params = array(), array $properties = array())
    {
        /* @var \ReflectionClass $reflection*/
        list($reflection, $dependencies) = $this->getDependencies($class);
        foreach ($params as $index => $param) {
            $dependencies[$index] = $param;
        }

        // 如果参数中有依赖类,就获取依赖类实例
        $dependencies = $this->resolveDependencies($dependencies);

        $object = $reflection->newInstanceArgs($dependencies);
        foreach ($properties as $name => $value) {
            $object->$name = $value;
        }
        return $object;
    }

    /**
     * 获取指定类构造方法所需的参数和参数依赖类
     *
     * @param string $class 类名、接口名
     * @return array 指定类的依赖参数
     */
    public function getDependencies($class)
    {
        $dependencies   = [];
        $reflection     = new \ReflectionClass($class);
        $constructor    = $reflection->getConstructor();
        if ($constructor !== null) {
            $parameters = $constructor->getParameters();
            foreach ($parameters as $param) {
                switch (true) {
                    case $param->isDefaultValueAvailable():// 如果是默认 直接取默认值
                        $dependencies[] = $param->getDefaultValue();
                        break;
                    case $param->getClass() === null:// 方法普通参数
                        $dependencies[] = null;
                        break;
                    default:// 参数中有依赖类
                        $dependencies[] = new DependenceClass($param->getClass());
                        break;
                }
            }
        }
        $this->_reflections[$class] = $reflection;
        $this->_dependencies[$class] = $dependencies;
        return [$reflection, $dependencies];
    }


    /**
     * 获取参数中的依赖类,用依赖类实例替换它
     * 通过对象实例替换依赖参数
     *
     * @param array $dependencies
     * @param null $reflection
     * @return array
     */
    public function resolveDependencies(array $dependencies, $reflection = null)
    {
        foreach ($dependencies as $index => $dependency) {
            if ($dependency instanceof DependenceClass) {
                if ($dependency->getReflectionClass() !== null) {
                    $name = $dependency->getReflectionClass()->getName();
                    $dependencies[$index] = $this->get($name);
                } elseif ($reflection !== null) {
                    $name = $reflection->getConstructor()->getParameters()[$index]->getName();
                    $class = $reflection->getName();
                    throw new ClassNotFoundException("Missing required parameter \"$name\" when instantiating \"$class\".");
                }
            }
        }
        return $dependencies;
    }

    /**
     * 获取所有名称的定义
     *
     * @return array
     */
    public function getDefinitions()
    {
        return $this->_definitions;
    }
}



