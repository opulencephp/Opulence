<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Orm\Tests\Ids\Accessors;

use Opulence\Orm\Ids\Accessors\IdAccessorRegistry;
use Opulence\Orm\IEntity;
use Opulence\Orm\OrmException;
use Opulence\Orm\Tests\Ids\Accessors\Mocks\Bar;
use Opulence\Orm\Tests\Ids\Accessors\Mocks\Foo;
use Opulence\Orm\Tests\Ids\Accessors\Mocks\User;

/**
 * Tests the Id accessor registry
 */
class IdAccessorRegistryTest extends \PHPUnit\Framework\TestCase
{
    /** @var IdAccessorRegistry The registry to use in tests */
    private $registry = null;
    /** @var User An entity to use in the tests */
    private $entity1 = null;

    /**
     * Sets up the tests
     */
    public function setUp()
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

    /**
     * Tests that the entity interface's accessors are automatically set
     */
    public function testEntityInterfaceAccessorsAutomaticallySet()
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

    /**
     * Tests getting an entity Id
     */
    public function testGettingEntityId()
    {
        $this->assertEquals(724, $this->registry->getEntityId($this->entity1));
    }

    /**
     * Tests getting an entity Id without registering a getter
     */
    public function testGettingEntityIdWithoutRegisteringGetter()
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
    public function testGettingIdWithReflectionForNonExistentProperty()
    {
        $this->expectException(OrmException::class);
        $this->registry->registerReflectionIdAccessors(Foo::class, 'doesNotExist');
        $this->registry->getEntityId(new Foo());
    }

    /**
     * Tests reflection accessors
     */
    public function testReflectionAccessors()
    {
        $this->registry->registerReflectionIdAccessors(Foo::class, 'id');
        $foo = new Foo();
        $this->registry->setEntityId($foo, 24);
        $this->assertEquals(24, $this->registry->getEntityId($foo));
    }

    /**
     * Tests reflection accessors
     */
    public function testReflectionAccessorsWithTwoClasses()
    {
        $this->registry->registerReflectionIdAccessors([Foo::class, Bar::class], 'id');
        $foo = new Foo();
        $bar = new Bar();
        $this->registry->setEntityId($foo, 24);
        $this->registry->setEntityId($bar, 42);
        $this->assertEquals(24, $this->registry->getEntityId($foo));
        $this->assertEquals(42, $this->registry->getEntityId($bar));
    }

    /**
     * Tests registering an array of class names
     */
    public function testRegisteringArrayOfClassNames()
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

    /**
     * Tests setting an entity Id
     */
    public function testSettingEntityId()
    {
        $this->registry->setEntityId($this->entity1, 333);
        $this->assertEquals(333, $this->entity1->getId());
    }

    /**
     * Tests setting an entity Id without registering a setter
     */
    public function testSettingEntityIdWithoutRegisteringGetter()
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
    public function testSettingIdWithReflectionForNonExistentProperty()
    {
        $this->expectException(OrmException::class);
        $this->registry->registerReflectionIdAccessors(Foo::class, 'doesNotExist');
        $this->registry->setEntityId(new Foo(), 24);
    }
}
