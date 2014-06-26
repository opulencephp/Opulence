<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the repository class
 */
namespace RDev\Models\ORM\Repositories;
use RDev\Models\ORM;
use RDev\Tests\Models\Databases\SQL\Mocks as SQLMocks;
use RDev\Tests\Models\ORM\DataMappers\Mocks as DataMapperMocks;
use RDev\Tests\Models\ORM\Mocks as ORMMocks;

class RepoTest extends \PHPUnit_Framework_TestCase
{
    /** @var ORMMocks\Entity An entity to use in the tests */
    private $entity1 = null;
    /** @var ORMMocks\Entity An entity to use in the tests */
    private $entity2 = null;
    /** @var ORM\UnitOfWork The unit of work to use in the tests */
    private $unitOfWork = null;
    /** @var DataMapperMocks\DataMapper The data mapper to use in tests */
    private $dataMapper = null;
    /** @var Repo The repository to test */
    private $repo = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $server = new SQLMocks\Server();
        $connection = new SQLMocks\Connection($server);
        $this->unitOfWork = new ORM\UnitOfWork($connection);
        $this->dataMapper = new DataMapperMocks\DataMapper();
        $this->entity1 = new ORMMocks\Entity(1, "foo");
        $this->entity2 = new ORMMocks\Entity(2, "bar");
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
        $this->setExpectedException("RDev\\Models\\ORM\\DataMappers\\Exceptions\\DataMapperException");
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
     * Tests getting an entity that doesn't exist by Id
     */
    public function testGettingEntityThatDoesNotExistById()
    {
        $this->setExpectedException("RDev\\Models\\ORM\\DataMappers\\Exceptions\\DataMapperException");
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