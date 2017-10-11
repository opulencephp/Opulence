<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Orm\Tests\DataMappers;

use Opulence\Orm\DataMappers\ICacheDataMapper;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\Orm\Ids\Accessors\IdAccessorRegistry;
use Opulence\Orm\OrmException;
use Opulence\Orm\Tests\DataMappers\Mocks\CachedSqlDataMapper;
use Opulence\Orm\Tests\DataMappers\Mocks\User;

/**
 * Tests the cached SQL data mapper
 */
class CachedSqlDataMapperTest extends \PHPUnit\Framework\TestCase
{
    /** @var CachedSQLDataMapper The data mapper to use for tests */
    private $dataMapper = null;
    /** @var User An entity to use for tests */
    private $entity1 = null;
    /** @var User An entity to use for tests */
    private $entity2 = null;
    /** @var User An entity to use for tests */
    private $entity3 = null;
    /** @var User An entity to use for tests */
    private $entity4 = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $idAccessorRegistry = new IdAccessorRegistry();
        $idAccessorRegistry->registerIdAccessors(User::class, function ($user) {
            /** @var User $user */
            return $user->getId();
        });
        $this->dataMapper = new CachedSqlDataMapper(null, null, $idAccessorRegistry);
        $this->entity1 = new User(123, 'foo');
        $this->entity2 = new User(456, 'bar');
        $this->entity3 = new User(789, 'baz');
        $this->entity4 = new User(101, 'blah');
    }

    /**
     * Tests adding an entity and committing to cache
     */
    public function testAddingEntityAndCommittingCache()
    {
        $this->dataMapper->add($this->entity1);
        $this->assertEquals($this->entity1, $this->dataMapper->getSqlDataMapper()->getById($this->entity1->getId()));
        $this->dataMapper->commit();
        $this->assertEquals($this->entity1, $this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
    }

    /**
     * Tests adding an entity without synchronizing to cache
     */
    public function testAddingEntityWithoutCommittingCache()
    {
        $this->dataMapper->add($this->entity1);
        $this->assertEquals($this->entity1, $this->dataMapper->getSqlDataMapper()->getById($this->entity1->getId()));
        $this->assertNull($this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
    }

    /**
     * Tests deleting an entity and committing to cache
     */
    public function testDeletingEntityAndCommittingCache()
    {
        $this->dataMapper->add($this->entity1);
        $this->dataMapper->delete($this->entity1);
        $this->dataMapper->commit();
        $this->assertNull($this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
    }

    /**
     * Tests deleting an entity without committing to cache
     */
    public function testDeletingEntityWithoutCommittingCache()
    {
        $this->dataMapper->add($this->entity1);
        $this->dataMapper->delete($this->entity1);
        $this->expectException(OrmException::class);
        $this->dataMapper->getSqlDataMapper()->getById($this->entity1->getId());
    }

    /**
     * Tests getting the cache data mapper
     */
    public function testGettingCacheDataMapper()
    {
        $this->assertInstanceOf(ICacheDataMapper::class, $this->dataMapper->getCacheDataMapper());
    }

    /**
     * Tests getting the SQL data mapper
     */
    public function testGettingSqlDataMapper()
    {
        $this->assertInstanceOf(SqlDataMapper::class, $this->dataMapper->getSqlDataMapper());
    }

    /**
     * Tests getting unsynced entities
     */
    public function testGettingUnsyncedEntities()
    {
        // Add entity 1 to both data mappers
        $this->dataMapper->getSqlDataMapper()->add($this->entity1);
        $this->dataMapper->getCacheDataMapper()->add($this->entity1);
        // Only add entity to the SQL data mapper
        $this->dataMapper->getSqlDataMapper()->add($this->entity2);
        // Add different versions of the same entity to the data mappers
        $this->dataMapper->getSqlDataMapper()->add($this->entity3);
        // Add an entity with slightly different data to see if it gets updated with the refresh call
        $differentEntity = clone $this->entity3;
        $differentEntity->setUsername('differentName');
        $this->dataMapper->getCacheDataMapper()->add($differentEntity);
        // This entity is ONLY in cache
        $this->dataMapper->getCacheDataMapper()->add($this->entity4);
        // This should synchronize cache and SQL
        $unsyncedEntities = $this->dataMapper->getUnsyncedEntities();
        $this->assertEquals([
            'missing' => [$this->entity2],
            'differing' => [$this->entity3],
            'additional' => [$this->entity4]
        ], $unsyncedEntities);
        // This should be the exact same instance because it was already in sync
        $this->assertSame($this->entity1, $this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
        // This entity should not appear in the cache data mapper
        $this->assertNull($this->dataMapper->getCacheDataMapper()->getById($this->entity2->getId()));
        // This entity should have been different than the one in the SQL data mapper
        $this->assertSame($differentEntity, $this->dataMapper->getCacheDataMapper()->getById($this->entity3->getId()));
        // This entity should only appear in the cache data mapper
        $this->assertSame($this->entity4, $this->dataMapper->getCacheDataMapper()->getById($this->entity4->getId()));
    }

    /**
     * Tests refreshing the cache
     */
    public function testRefreshingCache()
    {
        // Add entity 1 to both data mappers
        $this->dataMapper->getSqlDataMapper()->add($this->entity1);
        $this->dataMapper->getCacheDataMapper()->add($this->entity1);
        // Only add entity to the SQL data mapper
        $this->dataMapper->getSqlDataMapper()->add($this->entity2);
        // Add different versions of the same entity to the data mappers
        $this->dataMapper->getSqlDataMapper()->add($this->entity3);
        // Add an entity with slightly different data to see if it gets updated with the refresh call
        $differentEntity = clone $this->entity3;
        $differentEntity->setUsername('differentName');
        $this->dataMapper->getCacheDataMapper()->add($differentEntity);
        // This entity is ONLY in cache
        $this->dataMapper->getCacheDataMapper()->add($this->entity4);
        // This should synchronize cache and SQL
        $unsyncedEntities = $this->dataMapper->refreshCache();
        $this->assertEquals([
            'missing' => [$this->entity2],
            'differing' => [$this->entity3],
            'additional' => [$this->entity4]
        ], $unsyncedEntities);
        // This should be the exact same instance because it was already in sync
        $this->assertSame($this->entity1, $this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
        // This entity should have been added to cache because it was missing
        $this->assertEquals($this->entity2, $this->dataMapper->getCacheDataMapper()->getById($this->entity2->getId()));
        // This entity should have been synchronized because cache had a different version
        $this->assertEquals($this->entity3, $this->dataMapper->getCacheDataMapper()->getById($this->entity3->getId()));
        // This entity should have been removed because it only existed in cache
        $this->assertNull($this->dataMapper->getCacheDataMapper()->getById($this->entity4->getId()));
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
        $this->dataMapper->getCacheDataMapper()->delete($this->entity1);
        $this->dataMapper->refreshEntity($this->entity1->getId());
        $this->assertEquals($this->entity1, $this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
    }

    /**
     * Tests attempting to refresh an entity that returns null from SQL
     */
    public function testRefreshingNullEntity()
    {
        $this->expectException(OrmException::class);
        $this->dataMapper->refreshEntity($this->entity1->getId());
    }

    /**
     * Tests updating an entity and committing to cache
     */
    public function testUpdatingEntityAndCommittingCache()
    {
        $this->dataMapper->getSqlDataMapper()->add($this->entity1);
        $this->dataMapper->getCacheDataMapper()->add($this->entity1);
        $this->entity1->setUsername('bar');
        $this->dataMapper->update($this->entity1);
        $this->dataMapper->commit();
        $this->assertEquals($this->entity1, $this->dataMapper->getSqlDataMapper()->getById($this->entity1->getId()));
        $this->assertEquals($this->entity1, $this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
    }

    /**
     * Tests updating an entity without committing to cache
     */
    public function testUpdatingEntityWithoutCommittingCache()
    {
        $this->dataMapper->getSqlDataMapper()->add($this->entity1);
        $this->dataMapper->getCacheDataMapper()->add($this->entity1);
        /**
         * We have to clone the original entity so that when we set a property on it, it doesn't update the object
         * referenced by the mock data mappers
         */
        $entityClone = clone $this->entity1;
        $entityClone->setUsername('bar');
        $this->dataMapper->update($entityClone);
        $this->assertEquals($entityClone, $this->dataMapper->getSqlDataMapper()->getById($this->entity1->getId()));
        $this->assertNotEquals($entityClone, $this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
    }
}
