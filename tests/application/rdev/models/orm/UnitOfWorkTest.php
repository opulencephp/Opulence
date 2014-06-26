<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the unit of work
 */
namespace RDev\Models\ORM;
use RDev\Models;
use RDev\Models\Users;
use RDev\Tests\Models\Databases\SQL\Mocks as SQLMocks;
use RDev\Tests\Models\ORM\Mocks as ORMMocks;
use RDev\Tests\Models\ORM\DataMappers\Mocks as DataMapperMocks;

class UnitOfWorkTest extends \PHPUnit_Framework_TestCase
{
    /** @var UnitOfWork The unit of work to use in the tests */
    private $unitOfWork = null;
    /** @var DataMapperMocks\DataMapper The data mapper to use in tests */
    private $dataMapper = null;
    /** @var ORMMocks\Entity An entity to use in the tests */
    private $entity1 = null;
    /** @var ORMMocks\Entity An entity to use in the tests */
    private $entity2 = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $server = new SQLMocks\Server();
        $connection = new SQLMocks\Connection($server);
        $this->unitOfWork = new UnitOfWork($connection);
        $this->dataMapper = new DataMapperMocks\DataMapper();
        $this->entity1 = new ORMMocks\Entity(1, "foo");
        $this->entity2 = new ORMMocks\Entity(2, "bar");
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
        $this->entity1->setStringProperty("blah");
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
        $this->entity1->setStringProperty("blah");
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
        $this->assertFalse($this->unitOfWork->getManagedEntity(get_class($this->entity1), $this->entity1->getId()));
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
        $this->setExpectedException("RDev\\Models\\ORM\\DataMappers\\Exceptions\\DataMapperException");
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
        $this->setExpectedException("RDev\\Models\\ORM\\DataMappers\\Exceptions\\DataMapperException");
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
        $this->entity1->setStringProperty("blah");
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
}