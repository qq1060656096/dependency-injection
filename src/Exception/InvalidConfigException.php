<?php
namespace Zwei\DependencyInjection\Exception;

use Psr\Container\NotFoundExceptionInterface;

/**
 * 类定义异常
 *
 * Class InvalidConfigException
 * @package Zwei\DependencyInjection\Exception
 */
class InvalidConfigException extends \RuntimeException implements NotFoundExceptionInterface
{

}