<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the data mapper class for use in testing
 */
namespace RDev\Tests\Models\ORM\DataMappers\Mocks;
use RDev\Models;
use RDev\Models\Databases\SQL;
use RDev\Models\ORM;
use RDev\Models\ORM\DataMappers;
use RDev\Models\ORM\Ids;

class SQLDataMapper extends DataMappers\SQLDataMapper
{
    /** @var Models\IEntity[] The list of entities added */
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
    public function add(Models\IEntity &$entity)
    {
        $this->currId++;
        $entity->setId($this->currId);
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(Models\IEntity &$entity)
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
            throw new ORM\ORMException("No entity found with Id " . $id);
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
    public function update(Models\IEntity &$entity)
    {
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function loadEntity(array $hash, SQL\IConnection $connection)
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    protected function setIdGenerator()
    {
        $this->idGenerator = new Ids\IntSequenceIdGenerator("foo");
    }
} 