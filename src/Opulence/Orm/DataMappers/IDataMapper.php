<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\DataMappers;

use Opulence\Orm\OrmException;

/**
 * Defines the interface for data mappers to implement
 */
interface IDataMapper
{
    /**
     * Adds an entity to the database
     *
     * @param object $entity The entity to add
     * @throws OrmException Thrown if the entity couldn't be added
     */
    public function add($entity): void;

    /**
     * Deletes an entity
     *
     * @param object $entity The entity to delete
     * @throws OrmException Thrown if the entity couldn't be deleted
     */
    public function delete($entity): void;

    /**
     * Gets all the entities
     *
     * @return array The list of all the entities
     */
    public function getAll(): array;

    /**
     * Gets the entity with the input Id
     *
     * @param int|string $id The Id of the entity we're searching for
     * @return object The entity with the input Id
     * @throws OrmException Thrown if there was no entity with the input Id
     */
    public function getById($id);

    /**
     * Saves any changes made to an entity
     *
     * @param object $entity The entity to save
     * @throws OrmException Thrown if the entity couldn't be saved
     */
    public function update($entity): void;
}
