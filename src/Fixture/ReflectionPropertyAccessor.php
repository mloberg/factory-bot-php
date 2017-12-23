<?php
/**
 * Copyright (c) 2017 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FactoryBot;

use Closure;
use ReflectionClass;
use ReflectionProperty;
use Symfony\Component\PropertyAccess\Exception;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class ReflectionPropertyAccessor implements PropertyAccessorInterface
{
    /**
     * Reflection cache
     *
     * @var array
     */
    static $cache = [];

    /**
     * {@inheritdoc}
     */
    public function setValue(&$objectOrArray, $propertyPath, $value)
    {
        $this->assertObject($objectOrArray);

        $this->getReflectionProperty($objectOrArray, (string) $propertyPath)->setValue($objectOrArray, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($objectOrArray, $propertyPath)
    {
        $this->assertObject($objectOrArray);

        return $this->getReflectionProperty($objectOrArray, (string) $propertyPath)->getValue($objectOrArray);
    }

    /**
     * {@inheritdoc}
     */
    public function isWritable($objectOrArray, $propertyPath)
    {
        $this->assertObject($objectOrArray);

        try {
            $this->getReflectionProperty($objectOrArray, (string) $propertyPath);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isReadable($objectOrArray, $propertyPath)
    {
        $this->assertObject($objectOrArray);

        try {
            $this->getReflectionProperty($objectOrArray, (string) $propertyPath);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * Assert that this is an object
     *
     * @param $objectOrArray
     */
    private function assertObject($objectOrArray)
    {
        if (!is_object($objectOrArray)) {
            throw new Exception\RuntimeException(sprintf(
                '%s only supports object. %s given.',
                __CLASS__,
                gettype($objectOrArray)
            ));
        }
    }

    /**
     * Get reflection class
     *
     * @param object $object
     *
     * @return ReflectionClass
     */
    private function getReflectionClass($object): ReflectionClass
    {
        $class = get_class($object);

        return $this->cache($class, '__ref', function () use ($class) {
            return new ReflectionClass($class);
        });
    }

    /**
     * Get reflection property
     *
     * @param object $object
     * @param string $propertyPath
     *
     * @return ReflectionProperty
     */
    private function getReflectionProperty($object, string $propertyPath): ReflectionProperty
    {
        $class = get_class($object);

        return $this->cache($class, $propertyPath, function () use ($object, $propertyPath, $class) {
            $ref = $this->getReflectionClass($object);

            if ($ref->hasProperty($propertyPath)) {
                $property = $ref->getProperty($propertyPath);
                $property->setAccessible(true);

                return $property;
            }

            throw new Exception\NoSuchPropertyException(sprintf(
                '%s does not have a %s property.',
                $class,
                $propertyPath
            ));
        });
    }

    /**
     * Cache a result
     *
     * @param string  $class
     * @param string  $key
     * @param Closure $value
     *
     * @return mixed
     */
    private function cache(string $class, string $key, Closure $value)
    {
        if (isset(static::$cache[$class][$key])) {
            return static::$cache[$class][$key];
        }

        return static::$cache[$class][$key] = $value();
    }
}
