<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the cache data mapper class for use in testing
 */
namespace Opulence\Tests\ORM\DataMappers\Mocks;

use Opulence\ORM\IEntity;
use Opulence\ORM\DataMappers\ICacheDataMapper;

class CacheDataMapper implements ICacheDataMapper
{
    /** @var IEntity[] The list of entities added */
    protected $entities = [];

    public function __construct()
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function add(IEntity &$entity)
    {
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * @inheritdoc
     */
    public function delete(IEntity &$entity)
    {
        unset($this->entities[$entity->getId()]);
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->entities = [];
    }

    /**
     * @inheritdoc
     */
    public function getAll()
    {
        return array_values($this->entities);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function update(IEntity &$entity)
    {
        $this->entities[$entity->getId()] = $entity;
    }
} 