<?php
/**
 * Copyright (c) 2017 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

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
