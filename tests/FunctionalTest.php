<?php

declare(strict_types=1);

namespace Mlo\FactoryBot\Test;

use Doctrine\ORM\EntityManagerInterface;
use Mlo\FactoryBot\Factory;
use Mlo\FactoryBot\Test\Model\Bar;
use Mlo\FactoryBot\Test\Model\Foo;
use Mlo\FactoryBot\Test\Model\User;
use PHPUnit\Framework\TestCase;

class FunctionalTest extends TestCase
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var EntityManagerInterface
     */
    private $entityManger;

    protected function setUp()
    {
        $this->entityManger = $GLOBALS['entityManager'];
        $this->factory = Factory::builder()
            ->storeWithDoctrine($this->entityManger)
            ->loadFactories(__DIR__.'/factories')
            ->build();
    }

    public function testInvalidStateWillThrowException()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->factory->build(Foo::class)->states('non-existing-state')->make();
    }

    public function testWillPreferSetterOverReflection()
    {
        $fixture = $this->factory->make(Bar::class);

        $this->assertEquals(0, strpos($fixture->getBar(), 'bar_'));
    }

    public function testMakeMultiple()
    {
        $fixtures = $this->factory->build(Foo::class)->times(2)->make();

        $this->assertCount(2, $fixtures);
        $this->assertInstanceOf(Foo::class, $fixtures[0]);
        $this->assertInstanceOf(Foo::class, $fixtures[1]);
    }

    public function testFacade()
    {
        $fixture = $this->factory->build(User::class)->states('name')->make();

        $this->assertInstanceOf(User::class, $fixture);
        $this->assertNotNull($fixture->getName()->getFirst());
    }

    /**
     * @group database
     */
    public function testDoctrineWillRespectSetId()
    {
        $this->entityManger->beginTransaction();

        $user = $this->factory->create(User::class, [
            'id' => 100,
        ]);

        $this->assertEquals(100, $user->getId());

        $this->assertSame($user, $this->entityManger->find(User::class, 100));

        $this->entityManger->rollback();
    }

    /**
     * @group database
     */
    public function testDoctrineWillAssignIdIfNoneSet()
    {
        $this->entityManger->beginTransaction();

        $user = $this->factory->create(User::class);

        $this->assertNotNull($user->getId());

        $this->entityManger->rollback();
    }
}
