<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the data mapper class for use in testing
 */
namespace RDev\Tests\ORM\DataMappers\Mocks;
use RDev\Databases\SQL\IConnection;
use RDev\ORM\DataMappers\SQLDataMapper as BaseSQLDataMapper;
use RDev\ORM\Ids\IntSequenceIdGenerator;
use RDev\ORM\IEntity;
use RDev\ORM\ORMException;

class SQLDataMapper extends BaseSQLDataMapper
{
    /** @var IEntity[] The list of entities added */
    protected $entities = [];
    /** @var int The current Id */
    private $currId = 0;

    public function __construct()
    {
        $this->setIdGenerator();
    }

    /**
     * {@inheritdoc}
     */
    public function add(IEntity &$entity)
    {
        $this->currId++;
        $entity->setId($this->currId);
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(IEntity &$entity)
    {
        unset($this->entities[$entity->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        // We clone all the entities so that they get new object hashes
        $clonedEntities = [];

        foreach(array_values($this->entities) as $entity)
        {
            $clonedEntities[] = clone $entity;
        }

        return $clonedEntities;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        if(!isset($this->entities[$id]))
        {
            throw new ORMException("No entity found with Id " . $id);
        }

        return clone $this->entities[$id];
    }

    /**
     * @return int
     */
    public function getCurrId()
    {
        return $this->currId;
    }

    /**
     * {@inheritdoc}
     */
    public function update(IEntity &$entity)
    {
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadEntity(array $hash, IConnection $connection)
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    protected function setIdGenerator()
    {
        $this->idGenerator = new IntSequenceIdGenerator("foo");
    }
} 