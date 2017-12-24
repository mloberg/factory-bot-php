<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Storage;

class NullStorage implements StorageInterface
{
    /**
     * {@inheritdoc}
     */
    public function save($fixture)
    {
    }
}
