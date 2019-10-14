<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\tests;

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
    private EntityRegistry $entityRegistry;
    private User $entity1;
    private User $entity2;
    private User $entity3;
    private string $entity1HashId;
    private string $entity2HashId;
    private string $entity3HashId;

    protected function setUp(): void
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

    public function testCheckingIfEntityIsRegisteredAfterMakingChangesToIt(): void
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->entity1->setUsername('blah');
        $this->assertTrue($this->entityRegistry->isRegistered($this->entity1));
    }

    public function testCheckingIfEntityIsRegisteredWithoutRegisteringIdGetter(): void
    {
        $entity = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMockClassName('Foo')
            ->getMock();
        $this->assertFalse($this->entityRegistry->isRegistered($entity));
    }

    public function testClear(): void
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->entityRegistry->registerEntity($this->entity2);
        $this->entityRegistry->clear();
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity2));
        $this->assertEquals(EntityStates::NEVER_REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
        $this->assertEquals(EntityStates::NEVER_REGISTERED, $this->entityRegistry->getEntityState($this->entity2));
    }

    public function testClearingAlsoClearsAggregateRootChildFunctions(): void
    {
        $this->entityRegistry->registerAggregateRootCallback($this->entity1, $this->entity2, function ($root, $child) {
            throw new RuntimeException('Should not get here');
        });
        $this->entityRegistry->clear();
        $this->entityRegistry->runAggregateRootCallbacks($this->entity2);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }

    public function testDeregesteringAlsoRemovesAggregateRootChildFunction(): void
    {
        $this->entityRegistry->registerAggregateRootCallback($this->entity1, $this->entity2, function ($root, $child) {
            throw new RuntimeException('Should not get here');
        });
        $this->entityRegistry->deregisterEntity($this->entity2);
        $this->entityRegistry->runAggregateRootCallbacks($this->entity2);
        // Essentially just test that we got here
        $this->assertTrue(true);
    }

    public function testDeregisteringEntity(): void
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->entityRegistry->deregisterEntity($this->entity1);
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
        $this->assertEquals(EntityStates::UNREGISTERED, $this->entityRegistry->getEntityState($this->entity1));
    }

    public function testDeregisteringEntityWithoutRegisteringIdGetter(): void
    {
        $this->expectException(OrmException::class);
        $entity = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMockClassName('Foo')
            ->getMock();
        $this->entityRegistry->setState($entity, EntityStates::REGISTERED);
        $this->entityRegistry->deregisterEntity($entity);
    }

    public function testGettingClassName(): void
    {
        $this->assertEquals(get_class($this->entity1), $this->entityRegistry->getClassName($this->entity1));
    }

    public function testGettingEntities(): void
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->entityRegistry->registerEntity($this->entity2);
        $this->assertEquals([$this->entity1, $this->entity2], $this->entityRegistry->getEntities());
    }

    public function testGettingEntitiesWhenThereIsNotOne(): void
    {
        $this->assertEquals([], $this->entityRegistry->getEntities());
    }

    public function testGettingEntityStateForRegisteredEntity(): void
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->assertEquals(EntityStates::REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
    }

    public function testGettingEntityStateForUnregisteredEntity(): void
    {
        $this->assertEquals(EntityStates::NEVER_REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
    }

    public function testGettingEntityThatIsNotRegistered(): void
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->assertNull($this->entityRegistry->getEntity($className, $this->entity1->getId()));
    }

    public function testGettingObjectHashId(): void
    {
        $this->assertEquals(spl_object_hash($this->entity1), $this->entityRegistry->getObjectHashId($this->entity1));
    }

    public function testRegisteringEntityWithoutRegisteringIdGetter(): void
    {
        $this->expectException(OrmException::class);
        $entity = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->setMockClassName('Foo')
            ->getMock();
        $this->entityRegistry->registerEntity($entity);
    }

    public function testSettingState(): void
    {
        $this->entityRegistry->registerEntity($this->entity1);
        $this->entityRegistry->setState($this->entity1, EntityStates::DEQUEUED);
        $this->assertEquals(EntityStates::DEQUEUED, $this->entityRegistry->getEntityState($this->entity1));
    }

    public function testSettingTwoAggregateRootsForChild(): void
    {
        $this->entityRegistry->registerAggregateRootCallback(
            $this->entity1,
            $this->entity3,
            function ($aggregateRoot, $child) {
                /** @var User $aggregateRoot */
                /** @var User $child */
                $child->setAggregateRootId($aggregateRoot->getId());
            }
        );
        $this->entityRegistry->registerAggregateRootCallback(
            $this->entity2,
            $this->entity3,
            function ($aggregateRoot, $child) {
                /** @var User $aggregateRoot */
                /** @var User $child */
                $child->setSecondAggregateRootId($aggregateRoot->getId());
            }
        );
        $this->entityRegistry->runAggregateRootCallbacks($this->entity3);
        $this->assertEquals($this->entity1->getId(), $this->entity3->getAggregateRootId());
        $this->assertEquals($this->entity2->getId(), $this->entity3->getSecondAggregateRootId());
    }
}
