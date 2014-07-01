<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the cached SQL data mapper
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Tests\Models\ORM\DataMappers\Mocks as DataMapperMocks;
use RDev\Tests\Models\ORM\Mocks as ORMMocks;

class CachedSQLDataMapperTest extends \PHPUnit_Framework_TestCase
{
    /** @var DataMapperMocks\CachedSQLDataMapper The data mapper to use for tests */
    private $dataMapper = null;
    /** @var ORMMocks\Entity The entity to use for tests */
    private $entity = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->dataMapper = new DataMapperMocks\CachedSQLDataMapper();
        $this->entity = new ORMMocks\Entity(123, "foo");
    }

    /**
     * Tests adding an entity and synchronizing cache
     */
    public function testAddingEntityAndSynchronizingCache()
    {
        $this->dataMapper->add($this->entity);
        $this->assertEquals($this->entity, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity->getId()));
        $this->dataMapper->syncCache();
        $this->assertEquals($this->entity, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity->getId()));
    }

    /**
     * Tests adding an entity without synchronizing cache
     */
    public function testAddingEntityWithoutSynchronizingCache()
    {
        $this->dataMapper->add($this->entity);
        $this->assertEquals($this->entity, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity->getId()));
        $this->setExpectedException("RDev\\Models\\ORM\\Exceptions\\ORMException");
        $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity->getId());
    }

    /**
     * Tests deleting an entity and synchronizing cache
     */
    public function testDeletingEntityAndSynchronizingCache()
    {
        $this->dataMapper->add($this->entity);
        $this->dataMapper->delete($this->entity);
        $this->dataMapper->syncCache();
        $this->setExpectedException("RDev\\Models\\ORM\\Exceptions\\ORMException");
        $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity->getId());
    }

    /**
     * Tests deleting an entity without synchronizing cache
     */
    public function testDeletingEntityWithoutSynchronizingCache()
    {
        $this->dataMapper->add($this->entity);
        $this->dataMapper->delete($this->entity);
        $this->setExpectedException("RDev\\Models\\ORM\\Exceptions\\ORMException");
        $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity->getId());
    }

    /**
     * Tests updating an entity and synchronizing cache
     */
    public function testUpdatingEntityAndSynchronizingCache()
    {
        $this->dataMapper->getSQLDataMapperForTests()->add($this->entity);
        $this->dataMapper->getCacheDataMapperForTests()->add($this->entity);
        $this->entity->setStringProperty("bar");
        $this->dataMapper->update($this->entity);
        $this->dataMapper->syncCache();
        $this->assertEquals($this->entity, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity->getId()));
        $this->assertEquals($this->entity, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity->getId()));
    }

    /**
     * Tests updating an entity without synchronizing cache
     */
    public function testUpdatingEntityWithoutSynchronizingCache()
    {
        $this->dataMapper->getSQLDataMapperForTests()->add($this->entity);
        $this->dataMapper->getCacheDataMapperForTests()->add($this->entity);
        /**
         * We have to clone the original entity so that when we set a property on it, it doesn't update the object
         * referenced by the mock data mappers
         */
        $entityClone = clone $this->entity;
        $entityClone->setStringProperty("bar");
        $this->dataMapper->update($entityClone);
        $this->assertEquals($entityClone, $this->dataMapper->getSQLDataMapperForTests()->getById($this->entity->getId()));
        $this->assertNotEquals($entityClone, $this->dataMapper->getCacheDataMapperForTests()->getById($this->entity->getId()));
    }
} 