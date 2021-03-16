<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm\Tests\Repositories;

use Opulence\Databases\Tests\Mocks\Connection;
use Opulence\Databases\Tests\Mocks\Server;
use Opulence\Orm\ChangeTracking\ChangeTracker;
use Opulence\Orm\EntityRegistry;
use Opulence\Orm\Ids\Accessors\IdAccessorRegistry;
use Opulence\Orm\Ids\Generators\IIdGeneratorRegistry;
use Opulence\Orm\Ids\Generators\IntSequenceIdGenerator;
use Opulence\Orm\OrmException;
use Opulence\Orm\Repositories\Repository;
use Opulence\Orm\Tests\DataMappers\Mocks\SqlDataMapper;
use Opulence\Orm\Tests\Repositories\Mocks\User;
use Opulence\Orm\UnitOfWork;

/**
 * Tests the repository class
 */
class RepositoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var User An entity to use in the tests */
    private $entity1 = null;
    /** @var User An entity to use in the tests */
    private $entity2 = null;
    /** @var UnitOfWork The unit of work to use in the tests */
    private $unitOfWork = null;
    /** @var SQLDataMapper The data mapper to use in tests */
    private $dataMapper = null;
    /** @var Repository The repository to test */
    private $repo = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $idAccessorRegistry = new IdAccessorRegistry();
        $idAccessorRegistry->registerIdAccessors(
            User::class,
            function ($user) {
                /** @var User $user */
                return $user->getId();
            },
            function ($user, $id) {
                /** @var User $user */
                $user->setId($id);
            }
        );
        /** @var IIdGeneratorRegistry|\PHPUnit_Framework_MockObject_MockObject $idGeneratorRegistry */
        $idGeneratorRegistry = $this->createMock(IIdGeneratorRegistry::class);
        $idGeneratorRegistry->expects($this->any())
            ->method('getIdGenerator')
            ->with(User::class)
            ->willReturn(new IntSequenceIdGenerator('foo'));
        $changeTracker = new ChangeTracker();
        $server = new Server();
        $connection = new Connection($server);
        $entityRegistry = new EntityRegistry($idAccessorRegistry, $changeTracker);
        $this->unitOfWork = new UnitOfWork(
            $entityRegistry,
            $idAccessorRegistry,
            $idGeneratorRegistry,
            $changeTracker,
            $connection
        );
        $this->dataMapper = new SqlDataMapper();
        $this->entity1 = new User(1, 'foo');
        $this->entity2 = new User(2, 'bar');
        $this->repo = new Repository(get_class($this->entity1), $this->dataMapper, $this->unitOfWork);
    }

    /**
     * Tests adding an entity
     */
    public function testAddingEntity()
    {
        $this->repo->add($this->entity1);
        $this->unitOfWork->commit();
        $this->assertEquals($this->entity1, $this->repo->getById($this->entity1->getId()));
    }

    /**
     * Tests deleting an entity
     */
    public function testDeletingEntity()
    {
        $this->repo->add($this->entity1);
        $this->unitOfWork->commit();
        $this->repo->delete($this->entity1);
        $this->unitOfWork->commit();
        $this->expectException(OrmException::class);
        $this->repo->getById($this->entity1->getId());
    }

    /**
     * Tests getting all the entities
     */
    public function testGettingAll()
    {
        $this->repo->add($this->entity1);
        $this->repo->add($this->entity2);
        $this->unitOfWork->commit();
        $this->assertEquals([$this->entity1, $this->entity2], $this->repo->getAll());
    }

    /**
     * Tests getting all the entities after adding them in different transactions
     */
    public function testGettingAllAfterAddingEntitiesInDifferentTransactions()
    {
        $this->repo->add($this->entity1);
        $this->unitOfWork->commit();
        $this->repo->add($this->entity2);
        $this->unitOfWork->commit();
        $this->assertEquals([$this->entity1, $this->entity2], $this->repo->getAll());
    }

    /**
     * Tests getting an entity by Id
     */
    public function testGettingById()
    {
        $this->repo->add($this->entity1);
        $this->unitOfWork->commit();
        $this->assertEquals($this->entity1, $this->repo->getById($this->entity1->getId()));
    }

    /**
     * Tests the repo and unit of work to make sure the same instance of an already-managed entity is returned by getAll
     */
    public function testGettingEntityByIdAndThenAllEntities()
    {
        $this->repo->add($this->entity1);
        $this->unitOfWork->commit();
        /** @var User $entityFromGetById */
        $entityFromGetById = $this->repo->getById($this->entity1->getId());
        /** @var User[] $allEntities */
        $allEntities = $this->repo->getAll();
        /** @var User $entityFromGetAll */
        $entityFromGetAll = null;

        foreach ($allEntities as $entity) {
            $entity->setUsername('newUsername');

            if ($entity->getId() == $entityFromGetById->getId()) {
                $entityFromGetAll = $entity;
            }
        }

        foreach ($allEntities as $entity) {
            if ($entity->getId() == $entityFromGetById->getId()) {
                $this->assertSame($entityFromGetById, $entity);
                $this->assertEquals($entityFromGetAll->getUsername(), $entityFromGetById->getUsername());
            }
        }
    }

    /**
     * Tests getting an entity that doesn't exist by Id
     */
    public function testGettingEntityThatDoesNotExistById()
    {
        $this->expectException(OrmException::class);
        $this->repo->getById(123);
    }

    /**
     * Tests getting an entity that's in the data mapper but not the repo
     */
    public function testGettingEntityThatExistsInDataMapperButNotRepo()
    {
        $this->dataMapper->add($this->entity1);
        $this->assertEquals($this->entity1, $this->repo->getById($this->entity1->getId()));
    }
}
