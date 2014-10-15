<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the unit of work
 */
namespace RDev\Models\ORM;
use RDev\Models;
use RDev\Models\ORM;
use RDev\Models\Users;
use RDev\Tests\Models\Mocks as ModelMocks;
use RDev\Tests\Models\Databases\SQL\Mocks as SQLMocks;
use RDev\Tests\Models\ORM\DataMappers\Mocks as DataMapperMocks;

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    /** @var UnitOfWork The unit of work to use in the tests */
    private $unitOfWork = null;
    /** @var DataMapperMocks\SQLDataMapper The data mapper to use in tests */
    private $dataMapper = null;
    /** @var ModelMocks\User An entity to use in the tests */
    private $entity1 = null;
    /** @var ModelMocks\User An entity to use in the tests */
    private $entity2 = null;
    /** @var ModelMocks\User An entity to use in the tests */
    private $entity3 = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $server = new SQLMocks\Server();
        $connection = new SQLMocks\Connection($server);
        $this->unitOfWork = new UnitOfWork($connection);
        $this->dataMapper = new DataMapperMocks\SQLDataMapper();
        /**
         * The Ids are purposely unique so that we can identify them as such without having to first insert them to
         * assign unique Ids
         * They are also purposely set to 724 and 1987 so that they won't potentially overlap with any default values
         * set to the Ids
         */
        $this->entity1 = new ModelMocks\User(724, "foo");
        $this->entity2 = new ModelMocks\User(1987, "bar");
        $this->entity3 = new ModelMocks\User(345, "baz");
    }

    /**
     * Tests checking if multiple entities are managed
     */
    public function testCheckingIfEntitiesAreManaged()
    {
        $this->unitOfWork->manageEntities([$this->entity1, $this->entity2]);
        $this->assertTrue($this->unitOfWork->isManaged($this->entity1));
        $this->assertTrue($this->unitOfWork->isManaged($this->entity2));
    }

    /**
     * Tests checking if an entity is managed
     */
    public function testCheckingIfEntityIsManaged()
    {
        $this->unitOfWork->manageEntity($this->entity1);
        $this->assertTrue($this->unitOfWork->isManaged($this->entity1));
    }

    /**
     * Tests checking if an entity is still marked as managed after making changes to it
     */
    public function testCheckingIfEntityIsManagedAfterMakingChangesToIt()
    {
        $this->unitOfWork->manageEntity($this->entity1);
        $this->entity1->setUsername("blah");
        $this->assertTrue($this->unitOfWork->isManaged($this->entity1));
    }

    /**
     * Tests seeing if the unit of work picks up on an update made outside of it
     */
    public function testCheckingIfEntityUpdateIsDetected()
    {
        $className = get_class($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->manageEntity($this->entity1);
        $this->entity1->setUsername("blah");
        $reflectionClass = new \ReflectionClass($this->unitOfWork);
        $method = $reflectionClass->getMethod("checkForUpdates");
        $method->setAccessible(true);
        $method->invoke($this->unitOfWork);
        $scheduledFoUpdate = $this->unitOfWork->getScheduledEntityUpdates();
        $this->unitOfWork->commit();
        $this->assertTrue(in_array($this->entity1, $scheduledFoUpdate));
        $this->assertEquals($this->entity1, $this->unitOfWork->getManagedEntity($className, $this->entity1->getId()));
        $this->assertEquals($this->entity1, $this->dataMapper->getById($this->entity1->getId()));
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
     * Tests that a comparison function for two instances of a class are considered identical
     */
    public function testComparisonFunctionSaysTwoInstancesAreIdentical()
    {
        $className = get_class($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->manageEntity($this->entity1);
        $this->entity1->setUsername("not entity 1's username");
        $this->unitOfWork->registerComparisonFunction($className, function ($a, $b)
        {
            /** @var ModelMocks\User $a */
            /** @var ModelMocks\User $b */
            return $a->getId() == $b->getId();
        });
        $reflectionClass = new \ReflectionClass($this->unitOfWork);
        $method = $reflectionClass->getMethod("checkForUpdates");
        $method->setAccessible(true);
        $method->invoke($this->unitOfWork);
        $scheduledForUpdate = $this->unitOfWork->getScheduledEntityUpdates();
        $this->unitOfWork->commit();
        $this->assertFalse(in_array($this->entity1, $scheduledForUpdate));
        $this->assertEquals($this->entity1, $this->unitOfWork->getManagedEntity($className, $this->entity1->getId()));
    }

    /**
     * Tests that a comparison function for two instances of a class are not considered identical
     */
    public function testComparisonFunctionSaysTwoInstancesAreNotIdentical()
    {
        $className = get_class($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->manageEntity($this->entity1);
        $this->entity1->setUsername("not entity 1's username");
        $this->unitOfWork->registerComparisonFunction($className, function ($a, $b)
        {
            /** @var ModelMocks\User $a */
            /** @var ModelMocks\User $b */
            return $a->getUsername() == $b->getUsername();
        });
        $reflectionClass = new \ReflectionClass($this->unitOfWork);
        $method = $reflectionClass->getMethod("checkForUpdates");
        $method->setAccessible(true);
        $method->invoke($this->unitOfWork);
        $scheduledForUpdate = $this->unitOfWork->getScheduledEntityUpdates();
        $this->unitOfWork->commit();
        $this->assertTrue(in_array($this->entity1, $scheduledForUpdate));
        $this->assertEquals($this->entity1, $this->unitOfWork->getManagedEntity($className, $this->entity1->getId()));
        $this->assertEquals($this->entity1, $this->dataMapper->getById($this->entity1->getId()));
    }

    /**
     * Tests detaching a managed entity
     */
    public function testDetachingEntity()
    {
        $this->unitOfWork->manageEntity($this->entity1);
        $this->unitOfWork->detach($this->entity1);
        $this->assertFalse($this->unitOfWork->isManaged($this->entity1));
        $this->assertEquals(EntityStates::DETACHED, $this->unitOfWork->getEntityState($this->entity1));
    }

    /**
     * Tests detaching a managed entity after scheduling it for deletion, insertion, and update
     */
    public function testDetachingEntityAfterSchedulingForDeletionInsertionUpdate()
    {
        $this->unitOfWork->manageEntity($this->entity1);
        $this->unitOfWork->scheduleForDeletion($this->entity1);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->scheduleForUpdate($this->entity1);
        $this->unitOfWork->detach($this->entity1);
        $this->assertFalse($this->unitOfWork->isManaged($this->entity1));
        $this->assertEquals(EntityStates::DETACHED, $this->unitOfWork->getEntityState($this->entity1));
        $this->assertFalse(in_array($this->entity1, $this->unitOfWork->getScheduledEntityDeletions()));
        $this->assertFalse(in_array($this->entity1, $this->unitOfWork->getScheduledEntityInsertions()));
        $this->assertFalse(in_array($this->entity1, $this->unitOfWork->getScheduledEntityUpdates()));
    }

    /**
     * Tests disposing the unit of work
     */
    public function testDisposing()
    {
        $this->unitOfWork->manageEntity($this->entity1);
        $this->unitOfWork->dispose();
        $this->assertFalse($this->unitOfWork->isManaged($this->entity1));
        $this->assertEquals(EntityStates::UNMANAGED, $this->unitOfWork->getEntityState($this->entity1));
        $this->assertEquals([], $this->unitOfWork->getScheduledEntityDeletions());
        $this->assertEquals([], $this->unitOfWork->getScheduledEntityInsertions());
        $this->assertEquals([], $this->unitOfWork->getScheduledEntityUpdates());
    }

    /**
     * Tests getting a data mapper
     */
    public function testGettingDataMapper()
    {
        $className = get_class($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->assertEquals($this->dataMapper, $this->unitOfWork->getDataMapper($className));
    }

    /**
     * Tests getting the entity state for a managed entity
     */
    public function testGettingEntityStateForManagedEntity()
    {
        $this->unitOfWork->manageEntity($this->entity1);
        $this->assertEquals(EntityStates::MANAGED, $this->unitOfWork->getEntityState($this->entity1));
    }

    /**
     * Tests getting the entity state for an unmanaged entity
     */
    public function testGettingEntityStateForUnmanagedEntity()
    {
        $this->assertEquals(EntityStates::UNMANAGED, $this->unitOfWork->getEntityState($this->entity1));
    }

    /**
     * Tests getting an entity that isn't managed
     */
    public function testGettingEntityThatIsNotManaged()
    {
        $this->assertNull($this->unitOfWork->getManagedEntity(get_class($this->entity1), $this->entity1->getId()));
    }

    /**
     * Tests inserting and deleting an entity in a single transaction
     */
    public function testInsertingAndDeletingEntity()
    {
        $className = get_class($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->manageEntity($this->entity1);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->scheduleForDeletion($this->entity1);
        $this->unitOfWork->commit();
        $this->assertFalse($this->unitOfWork->isManaged($this->entity1));
        $this->assertEquals(EntityStates::DELETED, $this->unitOfWork->getEntityState($this->entity1));
        $this->setExpectedException("RDev\\Models\\ORM\\ORMException");
        $this->dataMapper->getById($this->entity1->getId());
    }

    /**
     * Tests making sure an unchanged managed entity isn't scheduled for update
     */
    public function testMakingSureUnchangedEntityIsNotScheduledForUpdate()
    {
        $className = get_class($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->manageEntity($this->entity1);
        $reflectionClass = new \ReflectionClass($this->unitOfWork);
        $method = $reflectionClass->getMethod("checkForUpdates");
        $method->setAccessible(true);
        $method->invoke($this->unitOfWork);
        $scheduledFoUpdate = $this->unitOfWork->getScheduledEntityUpdates();
        $this->assertFalse(in_array($this->entity1, $scheduledFoUpdate));
    }

    /**
     * Tests the post-commit hook for a cached data mapper
     */
    public function testPostCommitOnCachedDataMapper()
    {
        $className = get_class($this->entity1);
        $dataMapper = new DataMapperMocks\CachedSQLDataMapper();
        $this->unitOfWork->registerDataMapper($className, $dataMapper);
        $this->unitOfWork->manageEntity($this->entity1);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->commit();
        $this->assertEquals($this->entity1, $dataMapper->getSQLDataMapperForTests()->getById($this->entity1->getId()));
        $this->assertEquals($this->entity1, $dataMapper->getCacheDataMapperForTests()->getById($this->entity1->getId()));
    }

    /**
     * Tests scheduling a deletion for an entity
     */
    public function testSchedulingDeletionEntity()
    {
        $this->unitOfWork->registerDataMapper(get_class($this->entity1), $this->dataMapper);
        $this->unitOfWork->scheduleForDeletion($this->entity1);
        $reflectionClass = new \ReflectionClass($this->unitOfWork);
        $method = $reflectionClass->getMethod("checkForUpdates");
        $method->setAccessible(true);
        $method->invoke($this->unitOfWork);
        $scheduledFoDeletion = $this->unitOfWork->getScheduledEntityDeletions();
        $this->unitOfWork->commit();
        $this->assertTrue(in_array($this->entity1, $scheduledFoDeletion));
        $this->assertFalse($this->unitOfWork->isManaged($this->entity1));
        $this->assertEquals(EntityStates::DELETED, $this->unitOfWork->getEntityState($this->entity1));
        $this->setExpectedException("RDev\\Models\\ORM\\ORMException");
        $this->dataMapper->getById($this->entity1->getId());
    }

    /**
     * Tests scheduling an insertion for an entity
     */
    public function testSchedulingInsertionEntity()
    {
        $className = get_class($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->assertEquals(EntityStates::ADDED, $this->unitOfWork->getEntityState($this->entity1));
        $reflectionClass = new \ReflectionClass($this->unitOfWork);
        $method = $reflectionClass->getMethod("checkForUpdates");
        $method->setAccessible(true);
        $method->invoke($this->unitOfWork);
        $scheduledFoInsertion = $this->unitOfWork->getScheduledEntityInsertions();
        $expectedId = $this->dataMapper->getCurrId() + 1;
        $this->unitOfWork->commit();
        $this->assertTrue(in_array($this->entity1, $scheduledFoInsertion));
        $this->assertEquals($this->entity1, $this->unitOfWork->getManagedEntity($className, $this->entity1->getId()));
        $this->assertEquals(EntityStates::MANAGED, $this->unitOfWork->getEntityState($this->entity1));
        $this->assertEquals($this->entity1, $this->dataMapper->getById($this->entity1->getId()));
        $this->assertEquals($expectedId, $this->entity1->getId());
    }

    /**
     * Tests scheduling an update for an entity
     */
    public function testSchedulingUpdate()
    {
        $className = get_class($this->entity1);
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
        $this->assertEquals($this->entity1, $this->unitOfWork->getManagedEntity($className, $this->entity1->getId()));
        $this->assertEquals(EntityStates::MANAGED, $this->unitOfWork->getEntityState($this->entity1));
        $this->assertEquals($this->entity1, $this->dataMapper->getById($this->entity1->getId()));
    }

    /**
     * Tests setting the aggregate root on inserted entities
     */
    public function testSettingAggregateRootOnInsertedEntities()
    {
        $originalAggregateRootId = $this->entity1->getId();
        $className = get_class($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->scheduleForInsertion($this->entity2);
        $this->unitOfWork->registerAggregateRootChild($this->entity1, $this->entity2, function ($aggregateRoot, $child)
        {
            /** @var ModelMocks\User $aggregateRoot */
            /** @var ModelMocks\User $child */
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
        $className = get_class($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->scheduleForUpdate($this->entity2);
        $this->unitOfWork->registerAggregateRootChild($this->entity1, $this->entity2, function ($aggregateRoot, $child)
        {
            /** @var ModelMocks\User $aggregateRoot */
            /** @var ModelMocks\User $child */
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
        $className = get_class($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $this->unitOfWork->scheduleForInsertion($this->entity1);
        $this->unitOfWork->scheduleForInsertion($this->entity2);
        $this->unitOfWork->scheduleForInsertion($this->entity3);
        $this->unitOfWork->registerAggregateRootChild($this->entity1, $this->entity3, function ($aggregateRoot, $child)
        {
            /** @var ModelMocks\User $aggregateRoot */
            /** @var ModelMocks\User $child */
            $child->setAggregateRootId($aggregateRoot->getId());
        });
        $this->unitOfWork->registerAggregateRootChild($this->entity2, $this->entity3, function ($aggregateRoot, $child)
        {
            /** @var ModelMocks\User $aggregateRoot */
            /** @var ModelMocks\User $child */
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

        try
        {
            $server = new SQLMocks\Server();
            $connection = new SQLMocks\Connection($server);
            $connection->setToFailOnPurpose(true);
            $this->unitOfWork = new UnitOfWork($connection);
            $this->dataMapper = new DataMapperMocks\SQLDataMapper();
            $this->entity1 = new ModelMocks\User(1, "foo");
            $this->entity2 = new ModelMocks\User(2, "bar");
            $className = get_class($this->entity1);
            $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
            $this->unitOfWork->scheduleForInsertion($this->entity1);
            $this->unitOfWork->scheduleForInsertion($this->entity2);
            $this->unitOfWork->commit();
        }
        catch(ORM\ORMException $ex)
        {
            $exceptionThrown = true;
        }

        $this->assertTrue($exceptionThrown);
        $this->assertSame($this->dataMapper->getIdGenerator()->getEmptyValue(), $this->entity1->getId());
        $this->assertSame($this->dataMapper->getIdGenerator()->getEmptyValue(), $this->entity2->getId());
    }

    /**
     * Gets the entity after committing it
     *
     * @return ModelMocks\User The entity from the data mapper
     * @throws ORM\ORMException Thrown if there was an error committing the transaction
     */
    private function getInsertedEntity()
    {
        $className = get_class($this->entity1);
        $this->unitOfWork->registerDataMapper($className, $this->dataMapper);
        $foo = new ModelMocks\User(18175, "blah");
        $this->unitOfWork->scheduleForInsertion($foo);
        $this->unitOfWork->commit();

        return $this->unitOfWork->getManagedEntity($className, $foo->getId());
    }
}