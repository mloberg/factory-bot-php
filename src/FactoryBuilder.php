<?php

declare(strict_types=1);

namespace Mlo\FactoryBot;

use Doctrine\ORM\EntityManagerInterface;
use Faker\Generator as Faker;
use Mlo\FactoryBot\Storage\DoctrineStorage;

class FactoryBuilder
{
    /**
     * @var Faker;
     */
    private $faker;

    /**
     * @var callable
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
     * @param callable $storage
     *
     * @return FactoryBuilder
     */
    public function setStorage(callable $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    /**
     * Store with Doctrine's entity manager
     *
     * @param EntityManagerInterface $entityManager
     *
     * @return FactoryBuilder
     */
    public function storeWithDoctrine(EntityManagerInterface $entityManager): self
    {
        $this->storage = new DoctrineStorage($entityManager);

        return $this;
    }

    /**
     * Set factory paths
     *
     * @param array ...$paths
     *
     * @return FactoryBuilder
     */
    public function loadFactories(...$paths): self
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
            $this->storage ?? function () {
            }
        );

        if ($this->paths) {
            array_map([$factory, 'load'], $this->paths);
        }

        return $factory;
    }
}
