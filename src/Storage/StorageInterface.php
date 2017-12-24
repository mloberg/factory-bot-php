<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Storage;

interface StorageInterface
{
    /**
     * Persist object
     *
     * @param object $fixture
     *
     * @return void
     */
    public function save($fixture);
}
