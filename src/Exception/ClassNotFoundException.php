<?php
namespace Zwei\DependencyInjection\Exception;

use RuntimeException;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Class ClassNotFoundException
 * @package Zwei\DependencyInjection\Exception
 */
class ClassNotFoundException extends RuntimeException implements NotFoundExceptionInterface
{
}
