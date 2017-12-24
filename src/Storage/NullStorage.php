<?php
/**
 * Copyright (c) 2017 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

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
