<?php
/**
 * Copyright (c) 2017 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

declare(strict_types=1);

namespace Mlo\FactoryBot\Test;

use Mlo\FactoryBot\Factory;
use Mlo\FactoryBot\Fixture\Builder;
use Mlo\FactoryBot\Test\Mock\Storage;
use Mlo\FactoryBot\Test\Model\Foo;
use Mlo\FactoryBot\Test\Model\Name;
use Mlo\FactoryBot\Test\Model\User;
use PHPUnit\Framework\TestCase;

class FactoryTest extends TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Storage
     */
    private $storage;

    protected function setUp()
    {
        $this->storage = new Storage();
        $this->factory = new Factory(\Faker\Factory::create(), $this->storage);
    }

    public function testInvoke()
    {
        $this->factory->define(Foo::class, function () {
            return [
                'foo' => 'foo',
                'bar' => 'bar',
                'baz' => 'baz',
            ];
        });

        $builder = call_user_func($this->factory, Foo::class);

        $this->assertInstanceOf(Builder::class, $builder);

        $fixture = $builder->make();

        $this->assertInstanceOf(Foo::class, $fixture);
        $this->assertEquals('foo', $fixture->getFoo());
        $this->assertEquals('bar', $fixture->getBar());
        $this->assertEquals('baz', $fixture->getBaz());
    }

    public function testDefineWithName()
    {
        $factory = $this->factory->define(Foo::class, function () {
            return ['foo' => 'foobar'];
        }, 'bar');

        $this->assertSame($this->factory, $factory);

        $fixture = $factory(Foo::class, 'bar')->make();

        $this->assertInstanceOf(Foo::class, $fixture);
        $this->assertEquals('foobar', $fixture->getFoo());
        $this->assertNull($fixture->getBar());
    }

    public function testState()
    {
        $this->factory->define(Foo::class, function () {
            return ['foo' => 'foo'];
        });

        $factory = $this->factory->state(Foo::class, 'bar', function () {
            return ['bar' => 'barbaz'];
        });

        $this->assertSame($this->factory, $factory);

        $fixture = $factory(Foo::class)->make();

        $this->assertInstanceOf(Foo::class, $fixture);
        $this->assertEquals('foo', $fixture->getFoo());
        $this->assertNull($fixture->getBar());

        $fixture2 = $factory(Foo::class)->states('bar')->make();

        $this->assertInstanceOf(Foo::class, $fixture2);
        $this->assertEquals('foo', $fixture2->getFoo());
        $this->assertEquals('barbaz', $fixture2->getBar());
    }

    public function testInstantiator()
    {
        $this->factory->define(Foo::class, function () {
            return [
                'first' => 'Foo',
                'last' => 'Bar',
            ];
        });

        $factory = $this->factory->instantiator(Foo::class, function () {
            return new Name();
        });

        $this->assertSame($this->factory, $factory);

        $fixture = $factory(Foo::class)->make();

        $this->assertInstanceOf(Name::class, $fixture);
        $this->assertEquals('Foo', $fixture->getFirst());
        $this->assertEquals('Bar', $fixture->getLast());
    }

    public function testCallback()
    {
        $called = false;

        $this->factory->define(Foo::class, function () { return []; });
        $factory = $this->factory->callback(Foo::class, function (Foo $foo) use (&$called) {
            $called = true;
        });

        $this->assertSame($this->factory, $factory);

        $factory(Foo::class)->make();

        $this->assertTrue($called);
    }

    public function testCreate()
    {
        $this->factory->define(Foo::class, function () { return ['foo' => 'foo']; });

        $fixture = $this->factory->create(Foo::class, ['bar' => 'bar']);

        $this->assertTrue($this->storage->isSaved($fixture));
        $this->assertEquals('foo', $fixture->getFoo());
        $this->assertEquals('bar', $fixture->getBar());
    }

    public function testMake()
    {
        $this->factory->define(Foo::class, function () { return ['foo' => 'foo']; });

        $fixture = $this->factory->make(Foo::class, ['baz' => 'baz']);

        $this->assertFalse($this->storage->isSaved($fixture));
        $this->assertEquals('foo', $fixture->getFoo());
        $this->assertEquals('baz', $fixture->getBaz());
    }

    public function testBuild()
    {
        $this->factory->define(Foo::class, function () { return ['foo' => 'foo']; });

        $builder = $this->factory->build(Foo::class);

        $this->assertInstanceOf(Builder::class, $builder);

        $fixture = $builder->make();

        $this->assertInstanceOf(Foo::class, $fixture);
    }

    public function testBuildThrowsExceptionOnNonExistentDefinition()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->build('foobar');
    }

    public function testBuildThrowsExceptionOnNonExistentDefinitionName()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->define('foo', function () { return []; }, 'bar');

        $this->factory->build('foo');
    }

    public function testInvokeThrowsExceptionOnNonExistentDefinition()
    {
        $this->expectException(\InvalidArgumentException::class);

        call_user_func($this->factory, 'foo');
    }

    public function testLoads()
    {
        $this->factory->load(__DIR__.'/factories');

        $user = $this->factory->make(User::class);
        $foo = $this->factory->make(Foo::class);

        $this->assertInstanceOf(User::class, $user);
        $this->assertInstanceOf(Foo::class, $foo);
    }

    public function testLoadSingle()
    {
        $this->factory->load(__DIR__.'/factories/foo.php');

        $this->assertInstanceOf(Foo::class, $this->factory->make(Foo::class));

        $this->expectException(\InvalidArgumentException::class);

        $this->factory->make(User::class);
    }
}
