<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Fixture;

use Faker\Generator as Faker;
use Mlo\FactoryBot\Event;

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
     * @var callable
     */
    private $storage;

    /**
     * @var array
     */
    private $events;

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
     * @param string   $class
     * @param callable $definition
     * @param array    $states
     * @param callable $instantiator
     * @param Faker    $faker
     * @param callable $storage
     * @param array    $events
     */
    public function __construct(
        string $class,
        callable $definition,
        array $states,
        callable $instantiator,
        Faker $faker,
        callable $storage,
        array $events
    ) {
        $this->class = $class;
        $this->definition = $definition;
        $this->states = $states;
        $this->instantiator = $instantiator;
        $this->faker = $faker;
        $this->storage = $storage;
        $this->events = $events;
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
     * Store results
     *
     * @param array $results
     */
    private function store(array $results)
    {
        array_map(function ($result) {
            $this->fireEvent(Event::SAVE, $result);
            call_user_func($this->storage, $result);
            $this->fireEvent(Event::SAVED, $result);
        }, $results);
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
        $definition = iterator_to_array($this->expandAttributes(
            $instance,
            $this->getAttributes($attributes)
        ));

        $this->fireEvent(Event::CREATE, $instance, $definition);
        (new Hydrator())($instance, $definition);
        $this->fireEvent(Event::CREATED, $instance);

        return $instance;
    }

    /**
     * Get raw attributes for fixture
     *
     * @param array $attributes
     *
     * @return array
     */
    private function getAttributes(array $attributes = [])
    {
        $definition = call_user_func($this->definition, $this->faker, $attributes);

        foreach ($this->activeStates as $state) {
            $definition = array_merge($definition, $this->applyState($state, $attributes));
        }

        return array_merge($definition, $attributes);
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
     * @param object $instance
     * @param array  $attributes
     *
     * @return \Generator
     */
    private function expandAttributes($instance, array $attributes): \Generator
    {
        foreach ($attributes as $key => $attribute) {
            if (is_callable($attribute) && !is_string($attribute)) {
                if (null !== ($value = $attribute($instance, $attributes))) {
                    yield $key => $value;
                }
            } elseif ($attribute instanceof static) {
                yield $key => $attribute->make();
            } else {
                yield $key => $attribute;
            }
        }
    }

    /**
     * Fire event
     *
     * @param string $event
     * @param array  ...$arguments
     */
    private function fireEvent(string $event, ...$arguments)
    {
        if ($event = ($this->events[$event] ?? null)) {
            $event(...$arguments);
        }
    }
}
