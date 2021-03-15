<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm\Tests;

use Opulence\Orm\ChangeTracking\ChangeTracker;
use Opulence\Orm\EntityRegistry;
use Opulence\Orm\EntityStates;
use Opulence\Orm\Ids\Accessors\IdAccessorRegistry;
use Opulence\Orm\OrmException;
use Opulence\Orm\Tests\Mocks\User;
use RuntimeException;

/**
 * Tests the entity registry
 */
class EntityRegistryTest extends \PHPUnit\Framework\TestCase
{
    /** @var EntityRegistry The entity registry to use in tests */
    private $entityRegistry = null;
    /** @var User An entity to use in the tests */
    private $entity1 = null;
    /** @var User An entity to use in the tests */
    private $entity2 = null;
    /** @var User An entity to use in the tests */
    private $entity3 = null;
    /** @var string Entity 1's object hash Id */
    private $entity1HashId;
    /** @var string Entity 2's object hash Id */
    private $entity2HashId;
    /** @var string Entity 3's object hash Id */
    private $entity3HashId;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $idAccessorRegistry = new IdAccessorRegistry();
        $idAccessorRegistry->registerIdAccessors(
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
        $this->entityRegistry = new EntityRegistry($idAccessorRegistry, new ChangeTracker());
        /**
         * The Ids are purposely unique so that we can identify them as such without having to first insert them to
         * assign unique Ids
         * They are also purposely set to 724, 1987, and 345 so that they won't potentially overlap with any default
         * values set to the Ids
         */
        $this->entity1 = new User(724, 'foo');
        $this->entity2 = new User(1987, 'bar');
        $this->entity3 = new User(345, 'baz');
        $this->entity1HashId = $this->entityRegistry->getObjectHashId($this->entity1);
        $this->entity2HashId = $this->entityRegistry->getObjectHashId($this->entity2);
        $this->entity3HashId = $this->entityRegistry->getObjectHashId($this->entity3);
    }

    /**
     * Tests checking if an entity is still marked as registered after making changes to it
     */
    public function testCheckingIfEntityIsRegisteredAfterMakingChangesToIt()
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->entity1->setUsername('blah');
        $this->assertTrue($this->entityRegistry->isRegistered($this->entity1));
    }

    /**
     * Tests checking if an entity is registered without registering an Id getter
     */
    public function testCheckingIfEntityIsRegisteredWithoutRegisteringIdGetter()
    {
        $entity = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMockClassName('Foo')
            ->getMock();
        $this->assertFalse($this->entityRegistry->isRegistered($entity));
    }

    /**
     * Tests clearing the registry
     */
    public function testClear()
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->entityRegistry->registerEntity($this->entity2);
        $this->entityRegistry->clear();
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity2));
        $this->assertEquals(EntityStates::NEVER_REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
        $this->assertEquals(EntityStates::NEVER_REGISTERED, $this->entityRegistry->getEntityState($this->entity2));
    }

    /**
     * Tests clearing the registry also clears aggregate root child functions
     */
    public function testClearingAlsoClearsAggregateRootChildFunctions()
    {
        $this->entityRegistry->registerAggregateRootCallback($this->entity1, $this->entity2, function ($root, $child) {
            throw new RuntimeException('Should not get here');
        });
        $this->entityRegistry->clear();
        $this->entityRegistry->runAggregateRootCallbacks($this->entity2);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }

    /**
     * Tests deregestering also removes aggregate root child function
     */
    public function testDeregesteringAlsoRemovesAggregateRootChildFunction()
    {
        $this->entityRegistry->registerAggregateRootCallback($this->entity1, $this->entity2, function ($root, $child) {
            throw new RuntimeException('Should not get here');
        });
        $this->entityRegistry->deregisterEntity($this->entity2);
        $this->entityRegistry->runAggregateRootCallbacks($this->entity2);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }

    /**
     * Tests deregistering a registered entity
     */
    public function testDeregisteringEntity()
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->entityRegistry->deregisterEntity($this->entity1);
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
        $this->assertEquals(EntityStates::UNREGISTERED, $this->entityRegistry->getEntityState($this->entity1));
    }

    /**
     * Tests deregistering an entity without registering an Id getter
     */
    public function testDeregisteringEntityWithoutRegisteringIdGetter()
    {
        $this->expectException(OrmException::class);
        $entity = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMockClassName('Foo')
            ->getMock();
        $this->entityRegistry->setState($entity, EntityStates::REGISTERED);
        $this->entityRegistry->deregisterEntity($entity);
    }

    /**
     * Tests getting an object's class name
     */
    public function testGettingClassName()
    {
        $this->assertEquals(get_class($this->entity1), $this->entityRegistry->getClassName($this->entity1));
    }

    /**
     * Tests getting the entities
     */
    public function testGettingEntities()
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->entityRegistry->registerEntity($this->entity2);
        $this->assertEquals([$this->entity1, $this->entity2], $this->entityRegistry->getEntities());
    }

    /**
     * Tests getting the entities when there isn't one
     */
    public function testGettingEntitiesWhenThereIsNotOne()
    {
        $this->assertEquals([], $this->entityRegistry->getEntities());
    }

    /**
     * Tests getting the entity state for a registered entity
     */
    public function testGettingEntityStateForRegisteredEntity()
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->assertEquals(EntityStates::REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
    }

    /**
     * Tests getting the entity state for an unregistered entity
     */
    public function testGettingEntityStateForUnregisteredEntity()
    {
        $this->assertEquals(EntityStates::NEVER_REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
    }

    /**
     * Tests getting an entity that isn't registered
     */
    public function testGettingEntityThatIsNotRegistered()
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->assertNull($this->entityRegistry->getEntity($className, $this->entity1->getId()));
    }

    /**
     * Tests getting the object hash Id
     */
    public function testGettingObjectHashId()
    {
        $this->assertEquals(spl_object_hash($this->entity1), $this->entityRegistry->getObjectHashId($this->entity1));
    }

    /**
     * Tests registering an entity without registering an Id getter
     */
    public function testRegisteringEntityWithoutRegisteringIdGetter()
    {
        $this->expectException(OrmException::class);
        $entity = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMockClassName('Foo')
            ->getMock();
        $this->entityRegistry->registerEntity($entity);
    }

    /**
     * Tests setting an entity's state
     */
    public function testSettingState()
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->entityRegistry->setState($this->entity1, EntityStates::DEQUEUED);
        $this->assertEquals(EntityStates::DEQUEUED, $this->entityRegistry->getEntityState($this->entity1));
    }

    /**
     * Tests setting two aggregate roots for a single child
     */
    public function testSettingTwoAggregateRootsForChild()
    {
        $this->entityRegistry->registerAggregateRootCallback($this->entity1, $this->entity3,
            function ($aggregateRoot, $child) {
                /** @var User $aggregateRoot */
                /** @var User $child */
                $child->setAggregateRootId($aggregateRoot->getId());
            });
        $this->entityRegistry->registerAggregateRootCallback($this->entity2, $this->entity3,
            function ($aggregateRoot, $child) {
                /** @var User $aggregateRoot */
                /** @var User $child */
                $child->setSecondAggregateRootId($aggregateRoot->getId());
            });
        $this->entityRegistry->runAggregateRootCallbacks($this->entity3);
        $this->assertEquals($this->entity1->getId(), $this->entity3->getAggregateRootId());
        $this->assertEquals($this->entity2->getId(), $this->entity3->getSecondAggregateRootId());
    }
}
