<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm\Tests\DataMappers\Mocks;

use Opulence\Orm\DataMappers\ICacheDataMapper;

/**
 * Mocks the cache data mapper class for use in testing
 */
class CacheDataMapper implements ICacheDataMapper
{
    /** @var object[] The list of entities added */
    protected $entities = [];

    public function __construct()
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function add($entity)
    {
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * @inheritdoc
     */
    public function delete($entity)
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
    public function getAll() : array
    {
        return array_values($this->entities);
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->entities[$id])) {
            return null;
        }

        return $this->entities[$id];
    }

    /**
     * @inheritdoc
     */
    public function update($entity)
    {
        $this->entities[$entity->getId()] = $entity;
    }
}
