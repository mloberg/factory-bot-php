<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Test\Mock;

use Mlo\FactoryBot\Storage\StorageInterface;

class Storage implements StorageInterface
{
    /**
     * @var array
     */
    private $stored = [];

    public function save($fixture)
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
