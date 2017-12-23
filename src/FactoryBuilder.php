<?php
/**
 * Copyright (c) 2017 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FactoryBot;

use Faker\Generator as Faker;
use Mlo\FactoryBot\Storage\NullStorage;
use Mlo\FactoryBot\Storage\StorageInterface;

class FactoryBuilder
{
    /**
     * @var Faker;
     */
    private $faker;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var string[]
     */
    private $paths;

    /**
     * Set Faker
     *
     * @param Faker $faker
     *
     * @return FactoryBuilder
     */
    public function setFaker(Faker $faker): self
    {
        $this->faker = $faker;

        return $this;
    }

    /**
     * Set Storage
     *
     * @param StorageInterface $storage
     *
     * @return FactoryBuilder
     */
    public function setStorage(StorageInterface $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Set factory paths
     *
     * @param array ...$paths
     *
     * @return FactoryBuilder
     */
    public function setFactoryPath(...$paths): self
    {
        $this->paths = $paths;

        return $this;
    }

    /**
     * Build the factory
     *
     * @return Factory
     */
    public function build(): Factory
    {
        $factory = new Factory(
            $this->faker ?? \Faker\Factory::create(),
            $this->storage ?? new NullStorage()
        );

        if ($this->paths) {
            array_map([$factory, 'load'], $this->paths);
        }

        return $factory;
    }
}
