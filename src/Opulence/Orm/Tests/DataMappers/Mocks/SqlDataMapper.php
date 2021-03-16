<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Orm\Tests\DataMappers\Mocks;

use Opulence\Orm\DataMappers\SqlDataMapper as BaseSqlDataMapper;
use Opulence\Orm\OrmException;

/**
 * Mocks the data mapper class for use in testing
 */
class SqlDataMapper extends BaseSqlDataMapper
{
    /** @var object[] The list of entities added */
    protected $entities = [];
    /** @var int The current Id */
    private $currId = 0;

    public function __construct()
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function add($entity)
    {
        $this->currId++;
        $entity->setId($this->currId);
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
    public function getAll() : array
    {
        // We clone all the entities so that they get new object hashes
        $clonedEntities = [];

        foreach (array_values($this->entities) as $entity) {
            $clonedEntities[] = clone $entity;
        }

        return $clonedEntities;
    }

    /**
     * @inheritdoc
     */
    public function getById($id)
    {
        if (!isset($this->entities[$id])) {
            throw new OrmException('No entity found with Id ' . $id);
        }

        return clone $this->entities[$id];
    }

    /**
     * @return int
     */
    public function getCurrId() : int
    {
        return $this->currId;
    }

    /**
     * @inheritdoc
     */
    public function update($entity)
    {
        $this->entities[$entity->getId()] = $entity;
    }

    /**
     * @inheritdoc
     */
    protected function loadEntity(array $hash)
    {
        return new \stdClass();
    }
}
