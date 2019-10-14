<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\TestsTemp\DataMappers;

use Opulence\Orm\DataMappers\ICacheDataMapper;
use Opulence\Orm\DataMappers\SqlDataMapper;
use Opulence\Orm\Ids\Accessors\IdAccessorRegistry;
use Opulence\Orm\OrmException;
use Opulence\Orm\TestsTemp\DataMappers\Mocks\CachedSqlDataMapper;
use Opulence\Orm\TestsTemp\DataMappers\Mocks\User;

/**
 * Tests the cached SQL data mapper
 */
class CachedSqlDataMapperTest extends \PHPUnit\Framework\TestCase
{
    private CachedSqlDataMapper $dataMapper;
    private User $entity1;
    private User $entity2;
    private User $entity3;
    private User $entity4;

    protected function setUp(): void
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

    public function testAddingEntityAndCommittingCache(): void
    {
        $this->dataMapper->add($this->entity1);
        $this->assertEquals($this->entity1, $this->dataMapper->getSqlDataMapper()->getById($this->entity1->getId()));
        $this->dataMapper->commit();
        $this->assertEquals($this->entity1, $this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
    }

    public function testAddingEntityWithoutCommittingCache(): void
    {
        $this->dataMapper->add($this->entity1);
        $this->assertEquals($this->entity1, $this->dataMapper->getSqlDataMapper()->getById($this->entity1->getId()));
        $this->assertNull($this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
    }

    public function testDeletingEntityAndCommittingCache(): void
    {
        $this->dataMapper->add($this->entity1);
        $this->dataMapper->delete($this->entity1);
        $this->dataMapper->commit();
        $this->assertNull($this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
    }

    public function testDeletingEntityWithoutCommittingCache(): void
    {
        $this->dataMapper->add($this->entity1);
        $this->dataMapper->delete($this->entity1);
        $this->expectException(OrmException::class);
        $this->dataMapper->getSqlDataMapper()->getById($this->entity1->getId());
    }

    public function testGettingCacheDataMapper(): void
    {
        $this->assertInstanceOf(ICacheDataMapper::class, $this->dataMapper->getCacheDataMapper());
    }

    public function testGettingSqlDataMapper(): void
    {
        $this->assertInstanceOf(SqlDataMapper::class, $this->dataMapper->getSqlDataMapper());
    }

    public function testRefreshingEntity(): void
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

    public function testRefreshingNullEntity(): void
    {
        $this->expectException(OrmException::class);
        $this->dataMapper->refreshEntity($this->entity1->getId());
    }

    public function testUpdatingEntityAndCommittingCache(): void
    {
        $this->dataMapper->getSqlDataMapper()->add($this->entity1);
        $this->dataMapper->getCacheDataMapper()->add($this->entity1);
        $this->entity1->setUsername('bar');
        $this->dataMapper->update($this->entity1);
        $this->dataMapper->commit();
        $this->assertEquals($this->entity1, $this->dataMapper->getSqlDataMapper()->getById($this->entity1->getId()));
        $this->assertEquals($this->entity1, $this->dataMapper->getCacheDataMapper()->getById($this->entity1->getId()));
    }

    public function testUpdatingEntityWithoutCommittingCache(): void
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
