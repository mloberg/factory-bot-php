<?php
/**
 * Copyright (c) 2017 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

declare(strict_types=1);

namespace Mlo\FactoryBot\Test\Model;

class Bar extends Foo
{
    public function setBar($bar)
    {
        $this->bar = 'bar_'.$bar;
    }
}
