<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Mocks the cache data mapper class for use in testing
 */
namespace RDev\Tests\ORM\DataMappers\Mocks;
use RDev\ORM;
use RDev\ORM\DataMappers;

class CacheDataMapper implements DataMappers\ICacheDataMapper
{
    /** @var ORM\IEntity[] The list of entities added */
    protected $entities = [];

    public function __construct()
    {
        // Don't do anything
    }

    /**
     * {@inheritdoc}
     */
    public function add(ORM\IEntity &$entity)
    {
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ORM\IEntity &$entity)
    {
        unset($this->entities[$entity->getId()]);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->entities = [];
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return array_values($this->entities);
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        if(!isset($this->entities[$id]))
        {
            return null;
        }

        return $this->entities[$id];
    }

    /**
     * {@inheritdoc}
     */
    public function update(ORM\IEntity &$entity)
    {
        $this->entities[$entity->getId()] = $entity;
    }
} 