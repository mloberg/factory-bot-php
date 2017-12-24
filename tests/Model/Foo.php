<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Test\Model;

class Foo
{
    protected $foo;
    protected $bar;
    protected $baz;

    public function getFoo()
    {
        return $this->foo;
    }

    public function getBar()
    {
        return $this->bar;
    }

    public function getBaz()
    {
        return $this->baz;
    }
}
