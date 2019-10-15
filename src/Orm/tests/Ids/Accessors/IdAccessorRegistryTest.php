<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Tests\Ids\Accessors;

use Opulence\Orm\Ids\Accessors\IdAccessorRegistry;
use Opulence\Orm\IEntity;
use Opulence\Orm\OrmException;
use Opulence\Orm\Tests\Ids\Accessors\Mocks\Bar;
use Opulence\Orm\Tests\Ids\Accessors\Mocks\Foo;
use Opulence\Orm\Tests\Ids\Accessors\Mocks\User;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Id accessor registry
 */
class IdAccessorRegistryTest extends TestCase
{
    private IdAccessorRegistry $registry;
    private User $entity1;

    protected function setUp(): void
    {
        $this->registry = new IdAccessorRegistry();
        $this->registry->registerIdAccessors(
            User::class,
            function ($user) {
                /** @var User $user */
                return $user->getId();
            },
            function ($user, $id) {
                /** @var User $user */
                $user->setId($id);
            }
        );
        $this->entity1 = new User(724, 'foo');
    }

    public function testEntityInterfaceAccessorsAutomaticallySet(): void
    {
        $entity = $this->createMock(IEntity::class);
        $entity->expects($this->at(0))
            ->method('getId')
            ->willReturn(1);
        $entity->expects($this->at(1))
            ->method('setId')
            ->with(2);
        $entity->expects($this->at(2))
            ->method('getId')
            ->willReturn(2);
        $this->assertEquals(1, $this->registry->getEntityId($entity));
        $this->registry->setEntityId($entity, 2);
        $this->assertEquals(2, $this->registry->getEntityId($entity));
    }

    public function testGettingEntityId(): void
    {
        $this->assertEquals(724, $this->registry->getEntityId($this->entity1));
    }

    public function testGettingEntityIdWithoutRegisteringGetter(): void
    {
        $this->expectException(OrmException::class);
        $entity = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMockClassName('Foo')
            ->getMock();
        $this->registry->getEntityId($entity);
    }

    /**
     * Tests getting the Id with reflection for a non-existent property
     */
    public function testGettingIdWithReflectionForNonExistentProperty(): void
    {
        $this->expectException(OrmException::class);
        $this->registry->registerReflectionIdAccessors(Foo::class, 'doesNotExist');
        $this->registry->getEntityId(new Foo());
    }

    public function testReflectionAccessors(): void
    {
        $this->registry->registerReflectionIdAccessors(Foo::class, 'id');
        $foo = new Foo();
        $this->registry->setEntityId($foo, 24);
        $this->assertEquals(24, $this->registry->getEntityId($foo));
    }

    public function testReflectionAccessorsWithTwoClasses(): void
    {
        $this->registry->registerReflectionIdAccessors([Foo::class, Bar::class], 'id');
        $foo = new Foo();
        $bar = new Bar();
        $this->registry->setEntityId($foo, 24);
        $this->registry->setEntityId($bar, 42);
        $this->assertEquals(24, $this->registry->getEntityId($foo));
        $this->assertEquals(42, $this->registry->getEntityId($bar));
    }

    public function testRegisteringArrayOfClassNames(): void
    {
        $entity1 = $this->getMockBuilder(User::class)
            ->setMockClassName('FooEntity')
            ->disableOriginalConstructor()
            ->getMock();
        $entity1->expects($this->any())
            ->method('setId')
            ->with(123);
        $entity2 = $this->getMockBuilder(User::class)
            ->setMockClassName('BarEntity')
            ->disableOriginalConstructor()
            ->getMock();
        $entity2->expects($this->any())
            ->method('setId')
            ->with(456);
        $getter = function ($entity) {
            return 123;
        };
        $setter = function ($entity, $id) {
            /** @var User $entity */
            $entity->setId($id);
        };
        $this->registry->registerIdAccessors(['FooEntity', 'BarEntity'], $getter, $setter);
        $this->assertEquals(123, $this->registry->getEntityId($entity1));
        $this->registry->setEntityId($entity1, 123);
        $this->assertEquals(123, $this->registry->getEntityId($entity2));
        $this->registry->setEntityId($entity2, 456);
    }

    public function testSettingEntityId(): void
    {
        $this->registry->setEntityId($this->entity1, 333);
        $this->assertEquals(333, $this->entity1->getId());
    }

    public function testSettingEntityIdWithoutRegisteringGetter(): void
    {
        $this->expectException(OrmException::class);
        $entity = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMockClassName('Foo')
            ->getMock();
        $this->registry->setEntityId($entity, 24);
    }

    /**
     * Tests setting the Id with reflection for a non-existent property
     */
    public function testSettingIdWithReflectionForNonExistentProperty(): void
    {
        $this->expectException(OrmException::class);
        $this->registry->registerReflectionIdAccessors(Foo::class, 'doesNotExist');
        $this->registry->setEntityId(new Foo(), 24);
    }
}
