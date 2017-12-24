<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Test\Model;

class Bar extends Foo
{
    public function setBar($bar)
    {
        $this->bar = 'bar_'.$bar;
    }
}
