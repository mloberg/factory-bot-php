<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Storage;

interface StorageInterface
{
    /**
     * Persist object
     *
     * @param object $fixture
     */
    public function save($fixture);
}
