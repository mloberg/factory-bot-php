<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Fixture;

use Mlo\FactoryBot\Storage\StorageInterface;
use Faker\Generator as Faker;

class Builder
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var callable
     */
    private $definition;

    /**
     * @var array
     */
    private $states;

    /**
     * @var callable
     */
    private $instantiator;

    /**
     * @var Faker
     */
    private $faker;

    /**
     * @var StorageInterface
     */
    private $storage;

    /**
     * @var null|callable
     */
    private $callback;

    /**
     * @var int
     */
    private $amount = null;

    /**
     * @var array
     */
    private $activeStates = [];

    /**
     * Constructor
     *
     * @param string           $class
     * @param callable         $definition
     * @param array            $states
     * @param callable         $instantiator
     * @param Faker            $faker
     * @param StorageInterface $storage
     * @param callable|null    $callback
     */
    public function __construct(
        string $class,
        callable $definition,
        array $states,
        callable $instantiator,
        Faker $faker,
        StorageInterface $storage,
        callable $callback = null
    ) {
        $this->class        = $class;
        $this->definition   = $definition;
        $this->states       = $states;
        $this->instantiator = $instantiator;
        $this->faker        = $faker;
        $this->storage      = $storage;
        $this->callback     = $callback;
    }

    /**
     * Set amount of fixtures to build
     *
     * @param int $amount
     *
     * @return Builder
     */
    public function times(int $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Set states to apply
     *
     * @param string[] ...$states
     *
     * @return Builder
     */
    public function states(string ...$states): self
    {
        $this->activeStates = $states;

        return $this;
    }

    /**
     * Lazy load fixture
     *
     * @param array $attributes
     *
     * @return \Closure
     */
    public function lazy(array $attributes = []): \Closure
    {
        return function () use ($attributes) {
            return $this->create($attributes);
        };
    }

    /**
     * Create and persist fixture(s)
     *
     * @param array $attributes
     *
     * @return array|object
     */
    public function create(array $attributes = [])
    {
        $results = $this->make($attributes);

        is_array($results) ? $this->store($results) : $this->store([$results]);

        return $results;
    }

    /**
     * Create fixture(s)
     *
     * @param array $attributes
     *
     * @return array|object
     */
    public function make(array $attributes = [])
    {
        if (null === $this->amount) {
            return $this->makeInstance($attributes);
        }

        if ($this->amount < 1) {
            return [];
        }

        return array_map(function () use ($attributes) {
            return $this->makeInstance($attributes);
        }, range(1, $this->amount));
    }

    /**
     * Get fixture attributes
     *
     * @param array $attributes
     *
     * @return array
     */
    public function raw(array $attributes = []): array
    {
        if (null === $this->amount) {
            return $this->getRawAttributes($attributes);
        }

        if ($this->amount < 1) {
            return [];
        }

        return array_map(function () use ($attributes) {
            return $this->getRawAttributes($attributes);
        }, range(1, $this->amount));
    }

    /**
     * Store results
     *
     * @param array $results
     */
    private function store(array $results)
    {
        array_map(function ($result) {
            $this->storage->save($result);
        }, $results);
    }

    /**
     * Get raw attributes for fixture
     *
     * @param array $attributes
     *
     * @return array
     */
    private function getRawAttributes(array $attributes = [])
    {
        $definition = call_user_func($this->definition, $this->faker, $attributes);

        foreach ($this->activeStates as $state) {
            $definition = array_merge($definition, $this->applyState($state, $attributes));
        }

        return $this->expandAttributes(array_merge($definition, $attributes));
    }

    /**
     * Make a single fixture
     *
     * @param array $attributes
     *
     * @return object
     */
    private function makeInstance(array $attributes = [])
    {
        $instance = call_user_func($this->instantiator, $this->faker);
        $definition = $this->getRawAttributes($attributes);

        (new Hydrator())($instance, $definition);

        if ($this->callback) {
            call_user_func($this->callback, $instance);
        }

        return $instance;
    }

    /**
     * Apply states
     *
     * @param string $state
     * @param array  $attributes
     *
     * @return array
     */
    private function applyState(string $state, array $attributes): array
    {
        if (!isset($this->states[$state])) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid state for %s.', $state, $this->class));
        }

        $stateAttributes = $this->states[$state];

        if (!is_callable($stateAttributes)) {
            return $stateAttributes;
        }

        return call_user_func($stateAttributes, $this->faker, $attributes);
    }

    /**
     * Expand all attributes
     *
     * @param array $attributes
     *
     * @return array
     */
    private function expandAttributes(array $attributes): array
    {
        foreach ($attributes as &$attribute) {
            if (is_callable($attribute) && !is_string($attribute)) {
                $attribute = $attribute($attributes);
            }

            if ($attribute instanceof static) {
                $attribute = $attribute->make();
            }
        }

        return $attributes;
    }
}
