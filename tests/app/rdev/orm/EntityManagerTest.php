<?php
/**
 * Copyright (C) 2014 David Young
 * 
 * Tests the entity manager
 */
namespace RDev\ORM;
use RDev\Tests\Mocks;

class EntityManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityManager The entity manager to use in tests */
    private $entityManager = null;
    /** @var Mocks\User An entity to use in the tests */
    private $entity1 = null;
    /** @var Mocks\User An entity to use in the tests */
    private $entity2 = null;
    /** @var string Entity 1's object hash Id */
    private $entity1HashId;
    /** @var string Entity 2's object hash Id */
    private $entity2HashId;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->entityManager = new EntityManager();
        /**
         * The Ids are purposely unique so that we can identify them as such without having to first insert them to
         * assign unique Ids
         * They are also purposely set to 724 and 1987 so that they won't potentially overlap with any default values
         * set to the Ids
         */
        $this->entity1 = new Mocks\User(724, "foo");
        $this->entity2 = new Mocks\User(1987, "bar");
        $this->entity1HashId = $this->entityManager->getObjectHashId($this->entity1);
        $this->entity2HashId = $this->entityManager->getObjectHashId($this->entity2);
    }

    /**
     * Tests seeing if a change is detected with a comparison function
     */
    public function testCheckingForChangeWithComparisonFunction()
    {
        $className = get_class($this->entity1);
        $this->entityManager->manage($this->entity1);
        $this->entityManager->manage($this->entity2);
        $this->entity1->setUsername("not entity 1's username");
        $this->entityManager->registerComparisonFunction($className, function ($a, $b)
        {
            /** @var Mocks\User $a */
            /** @var Mocks\User $b */
            return $a->getId() == $b->getId();
        });
        $this->assertFalse($this->entityManager->hasChanged($this->entity1));
    }

    /**
     * Tests seeing if a change is detected without a comparison function
     */
    public function testCheckingForChangeWithoutComparisonFunction()
    {
        $this->entityManager->manage($this->entity1);
        $this->entity1->setUsername("blah");
        $this->assertTrue($this->entityManager->hasChanged($this->entity1));
    }

    /**
     * Tests checking for changes on an unmanaged entity
     */
    public function testCheckingForChangesOnUnmanagedEntity()
    {
        $this->setExpectedException("RDev\\ORM\\ORMException");
        $this->assertFalse($this->entityManager->isManaged($this->entity1));
        $this->entityManager->hasChanged($this->entity1);
    }

    /**
     * Tests checking that nothing has changed with a comparison function
     */
    public function testCheckingForNoChangeWithComparisonFunction()
    {
        $className = get_class($this->entity1);
        $this->entityManager->manage($this->entity1);
        $this->entityManager->registerComparisonFunction($className, function ($a, $b)
        {
            return false;
        });
        $this->assertTrue($this->entityManager->hasChanged($this->entity1));
    }

    /**
     * Tests checking that nothing has changed without a comparison function
     */
    public function testCheckingForNoChangeWithoutComparisonFunction()
    {
        $this->entityManager->manage($this->entity1);
        $this->assertFalse($this->entityManager->hasChanged($this->entity1));
    }

    /**
     * Tests checking if an entity is still marked as managed after making changes to it
     */
    public function testCheckingIfEntityIsManagedAfterMakingChangesToIt()
    {
        $this->entityManager->manage($this->entity1);
        $this->entity1->setUsername("blah");
        $this->assertTrue($this->entityManager->isManaged($this->entity1));
    }

    /**
     * Tests detaching a managed entity
     */
    public function testDetachingEntity()
    {
        $this->entityManager->manage($this->entity1);
        $this->entityManager->detach($this->entity1);
        $this->assertFalse($this->entityManager->isManaged($this->entity1));
        $this->assertEquals(EntityStates::DETACHED, $this->entityManager->getEntityState($this->entity1));
    }

    /**
     * Tests disposing
     */
    public function testDisposing()
    {
        $this->entityManager->manage($this->entity1);
        $this->entityManager->manage($this->entity2);
        $this->entityManager->dispose();
        $this->assertFalse($this->entityManager->isManaged($this->entity1));
        $this->assertFalse($this->entityManager->isManaged($this->entity2));
        $this->assertEquals(EntityStates::UNMANAGED, $this->entityManager->getEntityState($this->entity1));
        $this->assertEquals(EntityStates::UNMANAGED, $this->entityManager->getEntityState($this->entity2));
    }

    /**
     * Tests getting an object's class name
     */
    public function testGettingClassName()
    {
        $this->assertEquals(get_class($this->entity1), $this->entityManager->getClassName($this->entity1));
    }

    /**
     * Tests getting the entity state for a managed entity
     */
    public function testGettingEntityStateForManagedEntity()
    {
        $this->entityManager->manage($this->entity1);
        $this->assertEquals(EntityStates::MANAGED, $this->entityManager->getEntityState($this->entity1));
    }

    /**
     * Tests getting the entity state for an unmanaged entity
     */
    public function testGettingEntityStateForUnmanagedEntity()
    {
        $this->assertEquals(EntityStates::UNMANAGED, $this->entityManager->getEntityState($this->entity1));
    }

    /**
     * Tests getting an entity that isn't managed
     */
    public function testGettingEntityThatIsNotManaged()
    {
        $this->assertNull($this->entityManager->getManagedEntity(get_class($this->entity1), $this->entity1->getId()));
    }

    /**
     * Tests getting managed entities
     */
    public function testGettingManagedEntities()
    {
        $this->entityManager->manage($this->entity1);
        $this->entityManager->manage($this->entity2);
        $this->assertEquals([$this->entity1, $this->entity2], $this->entityManager->getManagedEntities());
    }

    /**
     * Tests getting the managed entities when there isn't one
     */
    public function testGettingManagedEntitiesWhenThereIsNotOne()
    {
        $this->assertEquals([], $this->entityManager->getManagedEntities());
    }

    /**
     * Tests getting the object hash Id
     */
    public function testGettingObjectHashId()
    {
        $this->assertEquals(spl_object_hash($this->entity1), $this->entityManager->getObjectHashId($this->entity1));
    }

    /**
     * Tests setting an entity's state
     */
    public function testSettingState()
    {
        $this->entityManager->manage($this->entity1);
        $this->entityManager->setState($this->entity1, EntityStates::DELETED);
        $this->assertEquals(EntityStates::DELETED, $this->entityManager->getEntityState($this->entity1));
    }
}