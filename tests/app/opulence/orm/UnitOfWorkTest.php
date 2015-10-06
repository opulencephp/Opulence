<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the unit of work
 */
namespace Opulence\ORM;

use Opulence\Tests\Mocks\User;
use Opulence\Tests\Databases\SQL\Mocks\Connection;
use Opulence\Tests\Databases\SQL\Mocks\Server;
use Opulence\Tests\ORM\DataMappers\Mocks\CachedSQLDataMapper;
use Opulence\Tests\ORM\DataMappers\Mocks\SQLDataMapper;

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    /** @var UnitOfWork The unit of work to use in the tests */
    private $unitOfWork = null;
    /** @var EntityRegistry The entity registry to use in tests */
    private $entityRegistry = null;
    /** @var SQLDataMapper The data mapper to use in tests */
    private $dataMapper = null;
    /** @var User An entity to use in the tests */
    private $entity1 = null;
    /** @var User An entity to use in the tests */
    private $entity2 = null;
    /** @var User An entity to use in the tests */
    private $entity3 = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $server = new Server();
        $connection = new Connection($server);
        $this->entityRegistry = new EntityRegistry();
        $this->unitOfWork = new UnitOfWork($this->entityRegistry, $connection);
        $this->dataMapper = new SQLDataMapper();
        /**
         * The Ids are purposely unique so that we can identify them as such without having to first insert them to
         * assign unique Ids
         * They are also purposely set to 724 and 1987 so that they won't potentially overlap with any default values
         * set to the Ids
         */
        $this->entity1 = new User(724, "foo");
        $this->entity2 = new User(1987, "bar");
        $this->entity3 = new User(345, "baz");
    }

    /**
     * Tests seeing if the unit of work picks up on an update made outside of it
     */
    public function testCheckingIfEntityUpdateIsDetected()
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->entityRegistry->register($this->entity1);
        $this->entity1->setUsername("blah");
        $reflectionClass = new \ReflectionClass($this->unitOfWork);
        $method = $reflectionClass->getMethod("checkForUpdates");
        $method->setAccessible(true);
        $method->invoke($this->unitOfWork);
        $scheduledFoUpdate = $this->unitOfWork->getScheduledEntityUpdates();
        $this->unitOfWork->commit();
        $this->assertTrue(in_array($this->entity1, $scheduledFoUpdate));
    }

    /**
     * Tests checking if an entity update is detected after copying its pointer to another variable
     */
    public function testCheckingIfEntityUpdateIsDetectedAfterCopyingPointer()
    {
        $foo = $this->getInsertedEntity();
        $bar = $foo;
        $bar->setUsername("bar");
        $this->unitOfWork->commit();
        $this->assertEquals($bar, $this->dataMapper->getById($foo->getId()));
    }

    /**
     * Tests checking if an entity update is detected after it is returned by a function
     */
    public function testCheckingIfEntityUpdateIsDetectedAfterReturningFromFunction()
    {
        $foo = $this->getInsertedEntity();
        $foo->setUsername("bar");
        $this->unitOfWork->commit();
        $this->assertEquals($foo, $this->dataMapper->getById($foo->getId()));
    }

    /**
     * Tests detaching a registered entity after scheduling it for deletion, insertion, and update
     */
    public function testDetachingEntityAfterSchedulingForDeletionInsertionUpdate()
    {
        $this->entityRegistry->register($this->entity1);
        $this->unitOfWork->scheduleForDeletion($this->entity1);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->scheduleForUpdate($this->entity1);
        $this->unitOfWork->detach($this->entity1);
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
        $this->assertEquals(EntityStates::UNREGISTERED, $this->entityRegistry->getEntityState($this->entity1));
        $this->assertFalse(in_array($this->entity1, $this->unitOfWork->getScheduledEntityDeletions()));
        $this->assertFalse(in_array($this->entity1, $this->unitOfWork->getScheduledEntityInsertions()));
        $this->assertFalse(in_array($this->entity1, $this->unitOfWork->getScheduledEntityUpdates()));
    }

    /**
     * Tests disposing of the unit of work
     */
    public function testDisposing()
    {
        $this->entityRegistry->register($this->entity1);
        $this->unitOfWork->dispose();
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
        $this->assertEquals(EntityStates::NEVER_REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
        $this->assertEquals([], $this->unitOfWork->getScheduledEntityDeletions());
        $this->assertEquals([], $this->unitOfWork->getScheduledEntityInsertions());
        $this->assertEquals([], $this->unitOfWork->getScheduledEntityUpdates());
    }

    /**
     * Tests getting a data mapper
     */
    public function testGettingDataMapper()
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->assertEquals($this->dataMapper, $this->unitOfWork->getDataMapper($className));
    }

    /**
     * Tests getting the entity registry
     */
    public function testGettingEntityRegistry()
    {
        $this->assertSame($this->entityRegistry, $this->unitOfWork->getEntityRegistry());
    }

    /**
     * Tests inserting and deleting an entity in a single transaction
     */
    public function testInsertingAndDeletingEntity()
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->entityRegistry->register($this->entity1);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->scheduleForDeletion($this->entity1);
        $this->unitOfWork->commit();
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
        $this->assertEquals(EntityStates::DEQUEUED, $this->entityRegistry->getEntityState($this->entity1));
        $this->setExpectedException(ORMException::class);
        $this->dataMapper->getById($this->entity1->getId());
    }

    /**
     * Tests making sure an unchanged registered entity isn't scheduled for update
     */
    public function testMakingSureUnchangedEntityIsNotScheduledForUpdate()
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->entityRegistry->register($this->entity1);
        $reflectionClass = new \ReflectionClass($this->unitOfWork);
        $method = $reflectionClass->getMethod("checkForUpdates");
        $method->setAccessible(true);
        $method->invoke($this->unitOfWork);
        $scheduledFoUpdate = $this->unitOfWork->getScheduledEntityUpdates();
        $this->assertFalse(in_array($this->entity1, $scheduledFoUpdate));
    }

    /**
     * Tests not setting the connection
     */
    public function testNotSettingConnection()
    {
        $this->setExpectedException(ORMException::class);
        $unitOfWork = new UnitOfWork($this->entityRegistry);
        $unitOfWork->commit();
    }

    /**
     * Tests the post-commit hook for a cached data mapper
     */
    public function testPostCommitOnCachedDataMapper()
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $dataMapper = new CachedSQLDataMapper();
        $this->unitOfWork->registerDataMapper($className, $dataMapper);
        $this->entityRegistry->register($this->entity1);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->commit();
        $this->assertEquals($this->entity1, $dataMapper->getSQLDataMapper()->getById($this->entity1->getId()));
        $this->assertEquals($this->entity1, $dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
    }

    /**
     * Tests scheduling a deletion for an entity
     */
    public function testSchedulingDeletionEntity()
    {
        $this->unitOfWork->registerDataMapper($this->entityRegistry->getClassName($this->entity1), $this->dataMapper);
        $this->unitOfWork->scheduleForDeletion($this->entity1);
        $reflectionClass = new \ReflectionClass($this->unitOfWork);
        $method = $reflectionClass->getMethod("checkForUpdates");
        $method->setAccessible(true);
        $method->invoke($this->unitOfWork);
        $scheduledFoDeletion = $this->unitOfWork->getScheduledEntityDeletions();
        $this->unitOfWork->commit();
        $this->assertTrue(in_array($this->entity1, $scheduledFoDeletion));
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
        $this->assertEquals(EntityStates::DEQUEUED, $this->entityRegistry->getEntityState($this->entity1));
        $this->setExpectedException(ORMException::class);
        $this->dataMapper->getById($this->entity1->getId());
    }

    /**
     * Tests scheduling an insertion for an entity
     */
    public function testSchedulingInsertionEntity()
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->assertEquals(EntityStates::QUEUED, $this->entityRegistry->getEntityState($this->entity1));
        $reflectionClass = new \ReflectionClass($this->unitOfWork);
        $method = $reflectionClass->getMethod("checkForUpdates");
        $method->setAccessible(true);
        $method->invoke($this->unitOfWork);
        $scheduledFoInsertion = $this->unitOfWork->getScheduledEntityInsertions();
        $expectedId = $this->dataMapper->getCurrId() + 1;
        $this->unitOfWork->commit();
        $this->assertTrue(in_array($this->entity1, $scheduledFoInsertion));
        $this->assertEquals($this->entity1, $this->entityRegistry->getEntity($className, $this->entity1->getId()));
        $this->assertEquals(EntityStates::REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
        $this->assertEquals($this->entity1, $this->dataMapper->getById($this->entity1->getId()));
        $this->assertEquals($expectedId, $this->entity1->getId());
    }

    /**
     * Tests scheduling an update for an entity
     */
    public function testSchedulingUpdate()
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->scheduleForUpdate($this->entity1);
        $this->entity1->setUsername("blah");
        $reflectionClass = new \ReflectionClass($this->unitOfWork);
        $method = $reflectionClass->getMethod("checkForUpdates");
        $method->setAccessible(true);
        $method->invoke($this->unitOfWork);
        $scheduledFoUpdate = $this->unitOfWork->getScheduledEntityUpdates();
        $this->unitOfWork->commit();
        $this->assertTrue(in_array($this->entity1, $scheduledFoUpdate));
        $this->assertEquals($this->entity1, $this->entityRegistry->getEntity($className, $this->entity1->getId()));
        $this->assertEquals(EntityStates::REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
        $this->assertEquals($this->entity1, $this->dataMapper->getById($this->entity1->getId()));
    }

    /**
     * Tests setting the aggregate root on inserted entities
     */
    public function testSettingAggregateRootOnInsertedEntities()
    {
        $originalAggregateRootId = $this->entity1->getId();
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->scheduleForInsertion($this->entity2);
        $this->unitOfWork->registerAggregateRootChild($this->entity1, $this->entity2,
            function ($aggregateRoot, $child) {
                /** @var User $aggregateRoot */
                /** @var User $child */
                $child->setAggregateRootId($aggregateRoot->getId());
            });
        $this->unitOfWork->commit();
        $this->assertNotEquals($originalAggregateRootId, $this->entity2->getAggregateRootId());
        $this->assertEquals($this->entity1->getId(), $this->entity2->getAggregateRootId());
    }

    /**
     * Tests setting the aggregate root on updated entities
     */
    public function testSettingAggregateRootOnUpdatedEntities()
    {
        $originalAggregateRootId = $this->entity1->getId();
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->scheduleForUpdate($this->entity2);
        $this->unitOfWork->registerAggregateRootChild($this->entity1, $this->entity2,
            function ($aggregateRoot, $child) {
                /** @var User $aggregateRoot */
                /** @var User $child */
                $child->setAggregateRootId($aggregateRoot->getId());
            });
        $this->unitOfWork->commit();
        $this->assertNotEquals($originalAggregateRootId, $this->entity2->getAggregateRootId());
        $this->assertEquals($this->entity1->getId(), $this->entity2->getAggregateRootId());
    }

    /**
     * Tests setting two aggregate roots for a single child
     */
    public function testSettingTwoAggregateRootsForChild()
    {
        $originalAggregateRootId = $this->entity1->getId();
        $originalSecondAggregateRootId = $this->entity2->getId();
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->scheduleForInsertion($this->entity2);
        $this->unitOfWork->scheduleForInsertion($this->entity3);
        $this->unitOfWork->registerAggregateRootChild($this->entity1, $this->entity3,
            function ($aggregateRoot, $child) {
                /** @var User $aggregateRoot */
                /** @var User $child */
                $child->setAggregateRootId($aggregateRoot->getId());
            });
        $this->unitOfWork->registerAggregateRootChild($this->entity2, $this->entity3,
            function ($aggregateRoot, $child) {
                /** @var User $aggregateRoot */
                /** @var User $child */
                $child->setSecondAggregateRootId($aggregateRoot->getId());
            });
        $this->unitOfWork->commit();
        $this->assertNotEquals($originalAggregateRootId, $this->entity3->getAggregateRootId());
        $this->assertNotEquals($originalSecondAggregateRootId, $this->entity3->getSecondAggregateRootId());
        $this->assertEquals($this->entity1->getId(), $this->entity3->getAggregateRootId());
        $this->assertEquals($this->entity2->getId(), $this->entity3->getSecondAggregateRootId());
    }

    /**
     * Tests to make sure that an entity's Id is being set after it's committed
     */
    public function testThatEntityIdIsBeingSetAfterCommit()
    {
        $foo = $this->getInsertedEntity();
        $this->assertEquals(1, $foo->getId());
    }

    /**
     * Tests an unsuccessful commit
     */
    public function testUnsuccessfulCommit()
    {
        $exceptionThrown = false;

        try {
            $server = new Server();
            $connection = new Connection($server);
            $connection->setToFailOnPurpose(true);
            $this->unitOfWork = new UnitOfWork($this->entityRegistry, $connection);
            $this->dataMapper = new SQLDataMapper();
            $this->entity1 = new User(1, "foo");
            $this->entity2 = new User(2, "bar");
            $className = $this->entityRegistry->getClassName($this->entity1);
            $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
            $this->unitOfWork->scheduleForInsertion($this->entity1);
            $this->unitOfWork->scheduleForInsertion($this->entity2);
            $this->unitOfWork->commit();
        }catch (ORMException $ex) {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
        $this->assertSame($this->dataMapper->getIdGenerator()->getEmptyValue(), $this->entity1->getId());
        $this->assertSame($this->dataMapper->getIdGenerator()->getEmptyValue(), $this->entity2->getId());
    }

    /**
     * Gets the entity after committing it
     *
     * @return User The entity from the data mapper
     * @throws ORMException Thrown if there was an error committing the transaction
     */
    private function getInsertedEntity()
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $foo = new User(18175, "blah");
        $this->unitOfWork->scheduleForInsertion($foo);
        $this->unitOfWork->commit();

        return $this->entityRegistry->getEntity($className, $foo->getId());
    }
}