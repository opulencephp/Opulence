<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the repository class
 */
namespace RDev\ORM\Repositories;
use RDev\ORM\EntityRegistry;
use RDev\ORM\UnitOfWork;
use RDev\Tests\Databases\SQL\Mocks\Connection;
use RDev\Tests\Databases\SQL\Mocks\Server;
use RDev\Tests\Mocks\User;
use RDev\Tests\ORM\DataMappers\Mocks\SQLDataMapper;

class RepoTest extends \PHPUnit_Framework_TestCase
{
    /** @var User An entity to use in the tests */
    private $entity1 = null;
    /** @var User An entity to use in the tests */
    private $entity2 = null;
    /** @var UnitOfWork The unit of work to use in the tests */
    private $unitOfWork = null;
    /** @var SQLDataMapper The data mapper to use in tests */
    private $dataMapper = null;
    /** @var Repo The repository to test */
    private $repo = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $server = new Server();
        $connection = new Connection($server);
        $entityStateManager = new EntityRegistry();
        $this->unitOfWork = new UnitOfWork($entityStateManager, $connection);
        $this->dataMapper = new SQLDataMapper();
        $this->entity1 = new User(1, "foo");
        $this->entity2 = new User(2, "bar");
        $this->repo = new Repo(get_class($this->entity1), $this->dataMapper, $this->unitOfWork);
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
        $this->setExpectedException("RDev\\ORM\\ORMException");
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

        foreach($allEntities as $entity)
        {
            $entity->setUsername("newUsername");

            if($entity->getId() == $entityFromGetById->getId())
            {
                $entityFromGetAll = $entity;
            }
        }

        foreach($allEntities as $entity)
        {
            if($entity->getId() == $entityFromGetById->getId())
            {
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
        $this->setExpectedException("RDev\\ORM\\ORMException");
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

    /**
     * Tests getting the unit of work
     */
    public function testGettingUnitOfWork()
    {
        $this->assertSame($this->unitOfWork, $this->repo->getUnitOfWork());
    }

    /**
     * Tests setting the data mapper
     */
    public function testSettingDataMapper()
    {
        $dataMapper = new SQLDataMapper();
        $this->repo->setDataMapper($dataMapper);
        $this->assertSame($dataMapper, $this->repo->getDataMapper());
        $this->repo->add($this->entity1);
        $this->unitOfWork->commit();
        $this->assertEquals($this->entity1, $this->repo->getById($this->entity1->getId()));
    }
} 