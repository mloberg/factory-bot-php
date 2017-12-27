<?php

namespace Mlo\FactoryBot;

use Mlo\FactoryBot\Fixture\Builder;

/**
 * @method static self define(string $class, callable $definition, string $name = 'default')
 * @method static self state(string $class, string $state, callable $definition)
 * @method static self instantiator(string $class, callable $instantiator, string $name = 'default')
 * @method static self callback(string $class, callable $callback, string $name = 'default')
 * @method static object create(string $class, array $attributes = [])
 * @method static object make(string $class, array $attributes = [])
 * @method static Builder build(string $class, string $name = 'default')
 */
class Facade
{
    /**
     * @var Factory
     */
    private static $instance;

    /**
     * Set Instance
     *
     * @param Factory $instance
     */
    public static function setInstance(Factory $instance)
    {
        static::$instance = $instance;
    }

    /**
     * Forward calls to instance
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        if (!static::$instance) {
            throw new \RuntimeException('Factory instance has not been set. Call "setInstance" first.');
        }

        return call_user_func_array([static::$instance, $name], $arguments);
    }
}
