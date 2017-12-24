<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Fixture;

use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

class Hydrator
{
    /**
     * @var PropertyAccessorInterface[]
     */
    private static $propertyAccessors;

    /**
     * Hydrate fixture
     *
     * @param object $instance
     * @param array  $attributes
     */
    public function __invoke($instance, array $attributes)
    {
        foreach ($attributes as $property => $value) {
            $this->setProperty($instance, $property, $value);
        }
    }

    /**
     * Set property on fixture
     *
     * @param object $instance
     * @param string $property
     * @param mixed  $value
     */
    private function setProperty($instance, string $property, $value)
    {
        if (false !== strpos($property, '.')) {
            $segments = explode('.', $property);
            $first = array_shift($segments);
            $rest = implode('.', $segments);

            $this->setProperty($this->getProperty($instance, $first), $rest, $value);

            return;
        }

        foreach ($this->propertyAccessors() as $propertyAccessor) {
            if ($propertyAccessor->isWritable($instance, $property)) {
                $propertyAccessor->setValue($instance, $property, $value);

                return;
            }
        }

        throw new \RuntimeException(sprintf(
            'Unable to set property "%s" on "%s".',
            $property,
            get_class($instance)
        ));
    }

    private function getProperty($instance, string $property)
    {
        foreach ($this->propertyAccessors() as $propertyAccessor) {
            if ($propertyAccessor->isReadable($instance, $property)) {
                return $propertyAccessor->getValue($instance, $property);
            }
        }

        throw new \RuntimeException(sprintf(
            'Unable to access property "%s" on "%s".',
            $property,
            get_class($instance)
        ));
    }

    /**
     * Get property accessors
     *
     * @return PropertyAccessorInterface[]
     */
    private function propertyAccessors(): array
    {
        if (!static::$propertyAccessors) {
            static::$propertyAccessors = [
                PropertyAccess::createPropertyAccessor(),
                new ReflectionPropertyAccessor(),
            ];
        }

        return static::$propertyAccessors;
    }
}
