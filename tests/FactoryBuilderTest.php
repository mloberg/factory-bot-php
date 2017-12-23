<?php
/**
 * Copyright (c) 2017 Matthew Loberg
 * Distributed under the MIT License (http://opensource.org/licenses/MIT)
 */

namespace Mlo\FactoryBot\Test;

use Faker\Generator;
use Mlo\FactoryBot\Factory;
use Mlo\FactoryBot\FactoryBuilder;
use Mlo\FactoryBot\Test\Mock\Storage;
use Mlo\FactoryBot\Test\Model\Foo;
use Mlo\FactoryBot\Test\Model\User;
use PHPUnit\Framework\TestCase;

class FactoryBuilderTest extends TestCase
{
    public function testCreateFactoryBuilderFromFactory()
    {
        $this->assertInstanceOf(FactoryBuilder::class, Factory::builder());
    }

    public function testCanCreateWithoutAnyOptions()
    {
        $this->assertInstanceOf(Factory::class, Factory::builder()->build());
    }

    public function testSetStorage()
    {
        $storage = new Storage();

        $factory = Factory::builder()
            ->setStorage($storage)
            ->build();

        $this->assertInstanceOf(Factory::class, $factory);

        $factory->define(Foo::class, function () { return ['foo' => 'foobar']; });

        $fixture = $factory(Foo::class)->create();

        $this->assertTrue($storage->isSaved($fixture));
    }

    public function testSetFakerInstance()
    {
        $faker = \Faker\Factory::create();

        $factory = Factory::builder()
            ->setFaker($faker)
            ->build();

        $this->assertInstanceOf(Factory::class, $factory);

        $factory->define(Foo::class, function (Generator $generator) use ($faker) {
            if ($faker !== $generator) {
                throw new \RuntimeException('Faker instance was not used.');
            }

            return ['foo' => 'foobar'];
        });

        $factory->make(Foo::class);
    }

    public function testLoadFactories()
    {
        $factory = Factory::builder()
            ->loadFactories(__DIR__.'/factories')
            ->build();

        $this->assertInstanceOf(Factory::class, $factory);

        $this->assertInstanceOf(User::class, $factory->make(User::class));
    }
}
