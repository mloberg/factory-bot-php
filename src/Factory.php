<?php

declare(strict_types=1);

namespace Mlo\FactoryBot;

use Mlo\FactoryBot\Fixture\Builder;
use Faker\Generator as Faker;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Factory
{
    /**
     * @var array
     */
    private $definitions = [];

    /**
     * @var array
     */
    private $states = [];

    /**
     * @var array
     */
    private $instantiators = [];

    /**
     * @var array
     */
    private $callbacks = [];

    /**
     * @var Faker
     */
    private $faker;

    /**
     * @var callable
     */
    private $storage;

    /**
     * Constructor
     *
     * @param Faker    $faker
     * @param callable $storage
     */
    public function __construct(Faker $faker, callable $storage)
    {
        $this->faker = $faker;
        $this->storage = $storage;
    }

    /**
     * Create a factory builder
     *
     * @return FactoryBuilder
     */
    public static function builder(): FactoryBuilder
    {
        return new FactoryBuilder();
    }

    /**
     * Create a fixture builder
     *
     * @param string $class
     * @param string $name
     *
     * @return Builder
     */
    public function __invoke(string $class, string $name = 'default'): Builder
    {
        return $this->build($class, $name);
    }

    /**
     * Define a factory
     *
     * @param string   $class
     * @param callable $attributes
     * @param string   $name
     *
     * @return Factory
     */
    public function define(string $class, callable $attributes, string $name = 'default'): self
    {
        $this->definitions[$class][$name] = $attributes;

        return $this;
    }

    /**
     * Add a state to a class
     *
     * @param string   $class
     * @param string   $state
     * @param callable $attributes
     *
     * @return Factory
     */
    public function state(string $class, string $state, callable $attributes): self
    {
        $this->states[$class][$state] = $attributes;

        return $this;
    }

    /**
     * Define how the fixture gets instantiated
     *
     * @param string   $class
     * @param callable $instantiator
     * @param string   $name
     *
     * @return Factory
     */
    public function instantiator(string $class, callable $instantiator, string $name = 'default'): self
    {
        $this->instantiators[$class][$name] = $instantiator;

        return $this;
    }

    /**
     * Add a callback to a fixture
     *
     * @param string   $class
     * @param callable $callback
     * @param string   $name
     *
     * @return Factory
     */
    public function callback(string $class, callable $callback, string $name = 'default'): self
    {
        $this->callbacks[$class][$name] = $callback;

        return $this;
    }

    /**
     * Create fixture
     *
     * @param string $class
     * @param array  $attributes
     *
     * @return object
     */
    public function create(string $class, array $attributes = [])
    {
        return $this->build($class)->create($attributes);
    }

    /**
     * Make fixture
     *
     * @param string $class
     * @param array  $attributes
     *
     * @return object
     */
    public function make(string $class, array $attributes = [])
    {
        return $this->build($class)->make($attributes);
    }

    /**
     * Build a fixture
     *
     * @param string $class
     * @param string $name
     *
     * @return Builder
     */
    public function build(string $class, string $name = 'default'): Builder
    {
        if (!isset($this->definitions[$class][$name])) {
            throw new \InvalidArgumentException(sprintf('Class %s::%s does not exist on this factory.', $class, $name));
        }

        return new Builder(
            $class,
            $this->definitions[$class][$name],
            $this->states[$class] ?? [],
            $this->instantiators[$class][$name] ?? function () use ($class) {
                return new $class();
            },
            $this->faker,
            $this->storage,
            $this->callbacks[$class][$name] ?? null
        );
    }

    /**
     * Load factory files
     *
     * @param string $path
     *
     * @return Factory
     */
    public function load(string $path): self
    {
        $factory = $this;

        if (is_dir($path)) {
            /** @var SplFileInfo $file */
            foreach (Finder::create()->files()->name('*.php')->in($path) as $file) {
                require $file->getRealPath();
            }
        } elseif (file_exists($path)) {
            require $path;
        }

        return $factory;
    }
}
