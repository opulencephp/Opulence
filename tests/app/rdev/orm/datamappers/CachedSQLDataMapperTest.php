<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the cached SQL data mapper
 */
namespace RDev\ORM\DataMappers;
use RDev\Tests\Mocks as ModelMocks;
use RDev\Tests\ORM\DataMappers\Mocks as DataMapperMocks;

class CachedSQLDataMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var DataMapperMocks\CachedSQLDataMapper The data mapper to use for tests */
    private $dataMapper = null;
    /** @var ModelMocks\User An entity to use for tests */
    private $entity1 = null;
    /** @var ModelMocks\User An entity to use for tests */
    private $entity2 = null;
    /** @var ModelMocks\User An entity to use for tests */
    private $entity3 = null;
    /** @var ModelMocks\User An entity to use for tests */
    private $entity4 = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->dataMapper = new DataMapperMocks\CachedSQLDataMapper();
        $this->entity1 = new ModelMocks\User(123, "foo");
        $this->entity2 = new ModelMocks\User(456, "bar");
        $this->entity3 = new ModelMocks\User(789, "baz");
        $this->entity4 = new ModelMocks\User(101, "blah");
    }

    /**
     * Tests adding an entity and committing to cache
     */
    public function testAddingEntityAndCommittingCache()
    {
        $this->dataMapper->add($this->entity1);
        $this->assertEquals($this->entity1, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity1->getId()));
        $this->dataMapper->commit();
        $this->assertEquals($this->entity1, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity1->getId()));
    }

    /**
     * Tests adding an entity without synchronizing to cache
     */
    public function testAddingEntityWithoutCommittingCache()
    {
        $this->dataMapper->add($this->entity1);
        $this->assertEquals($this->entity1, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity1->getId()));
        $this->assertNull($this->dataMapper->getCacheDataMapperForTests()->getById($this->entity1->getId()));
    }

    /**
     * Tests deleting an entity and committing to cache
     */
    public function testDeletingEntityAndCommittingCache()
    {
        $this->dataMapper->add($this->entity1);
        $this->dataMapper->delete($this->entity1);
        $this->dataMapper->commit();
        $this->assertNull($this->dataMapper->getCacheDataMapperForTests()->getById($this->entity1->getId()));
    }

    /**
     * Tests deleting an entity without committing to cache
     */
    public function testDeletingEntityWithoutCommittingCache()
    {
        $this->dataMapper->add($this->entity1);
        $this->dataMapper->delete($this->entity1);
        $this->setExpectedException("RDev\\ORM\\ORMException");
        $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity1->getId());
    }

    /**
     * Tests refreshing the cache
     */
    public function testRefreshingCache()
    {
        // Add entity 1 to both data mappers
        $this->dataMapper->getSQLDataMapperForTests()->add($this->entity1);
        $this->dataMapper->getCacheDataMapperForTests()->add($this->entity1);
        // Only add entity to the SQL data mapper
        $this->dataMapper->getSQLDataMapperForTests()->add($this->entity2);
        // Add different versions of the same entity to the data mappers
        $this->dataMapper->getSQLDataMapperForTests()->add($this->entity3);
        // Add an entity with slightly different data to see if it gets updated with the refresh call
        $differentEntity = clone $this->entity3;
        $differentEntity->setUsername("differentName");
        $this->dataMapper->getCacheDataMapperForTests()->add($differentEntity);
        // This entity is ONLY in cache
        $this->dataMapper->getCacheDataMapperForTests()->add($this->entity4);
        // This should synchronize cache and SQL
        $unsyncedEntities = $this->dataMapper->refreshCache();
        $this->assertEquals([
            "missing" => [$this->entity2],
            "differing" => [$this->entity3],
            "additional" => [$this->entity4]
        ], $unsyncedEntities);
        // This should be the exact same instance because it was already in sync
        $this->assertSame($this->entity1, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity1->getId()));
        // This entity should have been added to cache because it was missing
        $this->assertEquals($this->entity2, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity2->getId()));
        // This entity should have been synchronized because cache had a different version
        $this->assertEquals($this->entity3, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity3->getId()));
        // This entity should have been removed because it only existed in cache
        $this->assertNull($this->dataMapper->getCacheDataMapperForTests()->getById($this->entity4->getId()));
    }

    /**
     * Tests refreshing the cache when getting all the entities returns null
     */
    public function testRefreshingCacheWhenGetAllReturnsNull()
    {
        $this->dataMapper = new DataMapperMocks\CachedSQLDataMapper(
            null,
            new DataMapperMocks\CacheDataMapperThatReturnsNull()
        );
        $this->dataMapper->getSQLDataMapperForTests()->add($this->entity1);
        $unsyncedEntities = $this->dataMapper->refreshCache();
        $this->assertEquals([
            "missing" => [$this->entity1],
            "differing" => [],
            "additional" => []
        ], $unsyncedEntities);
    }

    /**
     * Tests refreshing an entity
     */
    public function testRefreshingEntity()
    {
        $this->dataMapper->add($this->entity1);
        /**
         * Manually delete the entity from cache in case there's a bug in the refresh code that prevents it from
         * being automatically deleted from cache
         */
        $this->dataMapper->getCacheDataMapperForTests()->delete($this->entity1);
        $this->dataMapper->refreshEntity($this->entity1->getId());
        $this->assertEquals($this->entity1, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity1->getId()));
    }

    /**
     * Tests updating an entity and committing to cache
     */
    public function testUpdatingEntityAndCommittingCache()
    {
        $this->dataMapper->getSQLDataMapperForTests()->add($this->entity1);
        $this->dataMapper->getCacheDataMapperForTests()->add($this->entity1);
        $this->entity1->setUsername("bar");
        $this->dataMapper->update($this->entity1);
        $this->dataMapper->commit();
        $this->assertEquals($this->entity1, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity1->getId()));
        $this->assertEquals($this->entity1, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity1->getId()));
    }

    /**
     * Tests updating an entity without committing to cache
     */
    public function testUpdatingEntityWithoutCommittingCache()
    {
        $this->dataMapper->getSQLDataMapperForTests()->add($this->entity1);
        $this->dataMapper->getCacheDataMapperForTests()->add($this->entity1);
        /**
         * We have to clone the original entity so that when we set a property on it, it doesn't update the object
         * referenced by the mock data mappers
         */
        $entityClone = clone $this->entity1;
        $entityClone->setUsername("bar");
        $this->dataMapper->update($entityClone);
        $this->assertEquals($entityClone, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity1->getId()));
        $this->assertNotEquals($entityClone, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity1->getId()));
    }
} 