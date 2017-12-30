<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Test\Mock;

class Storage
{
    /**
     * @var array
     */
    private $stored = [];

    public function __invoke($fixture)
    {
        $this->stored[] = $fixture;
    }

    public function isSaved($fixture)
    {
        return in_array($fixture, $this->stored, true);
    }

    public function clear()
    {
        $this->stored = [];
    }
}
