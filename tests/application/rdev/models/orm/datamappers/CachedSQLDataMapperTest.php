<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the cached SQL data mapper
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Tests\Models\Mocks as ModelMocks;
use RDev\Tests\Models\ORM\DataMappers\Mocks as DataMapperMocks;

class CachedSQLDataMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var DataMapperMocks\CachedSQLDataMapper The data mapper to use for tests */
    private $dataMapper = null;
    /** @var ModelMocks\User The entity to use for tests */
    private $entity = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->dataMapper = new DataMapperMocks\CachedSQLDataMapper();
        $this->entity = new ModelMocks\User(123, "foo");
    }

    /**
     * Tests adding an entity and committing to cache
     */
    public function testAddingEntityAndCommittingCache()
    {
        $this->dataMapper->add($this->entity);
        $this->assertEquals($this->entity, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity->getId()));
        $this->dataMapper->commit();
        $this->assertEquals($this->entity, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity->getId()));
    }

    /**
     * Tests adding an entity without synchronizing to cache
     */
    public function testAddingEntityWithoutCommittingCache()
    {
        $this->dataMapper->add($this->entity);
        $this->assertEquals($this->entity, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity->getId()));
        $this->setExpectedException("RDev\\Models\\ORM\\Exceptions\\ORMException");
        $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity->getId());
    }

    /**
     * Tests deleting an entity and committing to cache
     */
    public function testDeletingEntityAndCommittingCache()
    {
        $this->dataMapper->add($this->entity);
        $this->dataMapper->delete($this->entity);
        $this->dataMapper->commit();
        $this->setExpectedException("RDev\\Models\\ORM\\Exceptions\\ORMException");
        $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity->getId());
    }

    /**
     * Tests deleting an entity without committing to cache
     */
    public function testDeletingEntityWithoutCommittingCache()
    {
        $this->dataMapper->add($this->entity);
        $this->dataMapper->delete($this->entity);
        $this->setExpectedException("RDev\\Models\\ORM\\Exceptions\\ORMException");
        $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity->getId());
    }

    /**
     * Tests refreshing the cache
     */
    public function testRefreshingCache()
    {
        $this->dataMapper->add($this->entity);
        /**
         * Manually delete the entity from cache in case there's a bug in the refresh code that prevents it from
         * being automatically deleted from cache
         */
        $this->dataMapper->getCacheDataMapperForTests()->delete($this->entity);
        $this->dataMapper->refreshCache();
        $this->assertEquals($this->entity, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity->getId()));
    }

    /**
     * Tests updating an entity and committing to cache
     */
    public function testUpdatingEntityAndCommittingCache()
    {
        $this->dataMapper->getSQLDataMapperForTests()->add($this->entity);
        $this->dataMapper->getCacheDataMapperForTests()->add($this->entity);
        $this->entity->setUsername("bar");
        $this->dataMapper->update($this->entity);
        $this->dataMapper->commit();
        $this->assertEquals($this->entity, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity->getId()));
        $this->assertEquals($this->entity, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity->getId()));
    }

    /**
     * Tests updating an entity without committing to cache
     */
    public function testUpdatingEntityWithoutCommittingCache()
    {
        $this->dataMapper->getSQLDataMapperForTests()->add($this->entity);
        $this->dataMapper->getCacheDataMapperForTests()->add($this->entity);
        /**
         * We have to clone the original entity so that when we set a property on it, it doesn't update the object
         * referenced by the mock data mappers
         */
        $entityClone = clone $this->entity;
        $entityClone->setUsername("bar");
        $this->dataMapper->update($entityClone);
        $this->assertEquals($entityClone, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity->getId()));
        $this->assertNotEquals($entityClone, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity->getId()));
    }
} 