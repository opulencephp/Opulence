<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Tests the entity registry
 */
namespace RDev\ORM;
use RDev\Tests\Mocks;

class EntityRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var EntityRegistry The entity registry to use in tests */
    private $entityRegistry = null;
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
        $this->entityRegistry = new EntityRegistry();
        /**
         * The Ids are purposely unique so that we can identify them as such without having to first insert them to
         * assign unique Ids
         * They are also purposely set to 724 and 1987 so that they won't potentially overlap with any default values
         * set to the Ids
         */
        $this->entity1 = new Mocks\User(724, "foo");
        $this->entity2 = new Mocks\User(1987, "bar");
        $this->entity1HashId = $this->entityRegistry->getObjectHashId($this->entity1);
        $this->entity2HashId = $this->entityRegistry->getObjectHashId($this->entity2);
    }

    /**
     * Tests seeing if a change is detected with a comparison function
     */
    public function testCheckingForChangeWithComparisonFunction()
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->entityRegistry->register($this->entity1);
        $this->entityRegistry->register($this->entity2);
        $this->entity1->setUsername("not entity 1's username");
        $this->entityRegistry->registerComparisonFunction($className, function ($a, $b)
        {
            /** @var Mocks\User $a */
            /** @var Mocks\User $b */
            return $a->getId() == $b->getId();
        });
        $this->assertFalse($this->entityRegistry->hasChanged($this->entity1));
    }

    /**
     * Tests seeing if a change is detected without a comparison function
     */
    public function testCheckingForChangeWithoutComparisonFunction()
    {
        $this->entityRegistry->register($this->entity1);
        $this->entity1->setUsername("blah");
        $this->assertTrue($this->entityRegistry->hasChanged($this->entity1));
    }

    /**
     * Tests checking for changes on an unregistered entity
     */
    public function testCheckingForChangesOnUnregisteredEntity()
    {
        $this->setExpectedException("RDev\\ORM\\ORMException");
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
        $this->entityRegistry->hasChanged($this->entity1);
    }

    /**
     * Tests checking that nothing has changed with a comparison function
     */
    public function testCheckingForNoChangeWithComparisonFunction()
    {
        $className = $this->entityRegistry->getClassName($this->entity1);
        $this->entityRegistry->register($this->entity1);
        $this->entityRegistry->registerComparisonFunction($className, function ($a, $b)
        {
            return false;
        });
        $this->assertTrue($this->entityRegistry->hasChanged($this->entity1));
    }

    /**
     * Tests checking that nothing has changed without a comparison function
     */
    public function testCheckingForNoChangeWithoutComparisonFunction()
    {
        $this->entityRegistry->register($this->entity1);
        $this->assertFalse($this->entityRegistry->hasChanged($this->entity1));
    }

    /**
     * Tests checking if an entity is still marked as registered after making changes to it
     */
    public function testCheckingIfEntityIsRegisteredAfterMakingChangesToIt()
    {
        $this->entityRegistry->register($this->entity1);
        $this->entity1->setUsername("blah");
        $this->assertTrue($this->entityRegistry->isRegistered($this->entity1));
    }

    /**
     * Tests clearing the registry
     */
    public function testClear()
    {
        $this->entityRegistry->register($this->entity1);
        $this->entityRegistry->register($this->entity2);
        $this->entityRegistry->clear();
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity2));
        $this->assertEquals(EntityStates::NEVER_REGISTERED, $this->entityRegistry->getEntityState($this->entity1));
        $this->assertEquals(EntityStates::NEVER_REGISTERED, $this->entityRegistry->getEntityState($this->entity2));
    }

    /**
     * Tests deregistering a registered entity
     */
    public function testDeregisteringEntity()
    {
        $this->entityRegistry->register($this->entity1);
        $this->entityRegistry->deregister($this->entity1);
        $this->assertFalse($this->entityRegistry->isRegistered($this->entity1));
        $this->assertEquals(EntityStates::UNREGISTERED, $this->entityRegistry->getEntityState($this->entity1));
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
        $this->entityRegistry->register($this->entity1);
        $this->entityRegistry->register($this->entity2);
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
        $this->entityRegistry->register($this->entity1);
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
     * Tests setting an entity's state
     */
    public function testSettingState()
    {
        $this->entityRegistry->register($this->entity1);
        $this->entityRegistry->setState($this->entity1, EntityStates::DEQUEUED);
        $this->assertEquals(EntityStates::DEQUEUED, $this->entityRegistry->getEntityState($this->entity1));
    }
}