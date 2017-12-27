<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Test\Model;

class Bar extends Foo
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->baz = uniqid();
    }

    public function setBar($bar)
    {
        $this->bar = 'bar_'.$bar;
    }
}
