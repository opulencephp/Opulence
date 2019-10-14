<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\Repositories;

use Opulence\Orm\OrmException;

/**
 * Defines the interface for repositories to implement
 */
interface IRepository
{
    /**
     * Adds an entity to the repo
     *
     * @param object $entity The entity to add
     * @throws OrmException Thrown if the entity couldn't be added
     */
    public function add(object $entity): void;

    /**
     * Deletes an entity from the repo
     *
     * @param object $entity The entity to delete
     * @throws OrmException Thrown if the entity couldn't be deleted
     */
    public function delete(object $entity): void;

    /**
     * Gets the entity with the input Id
     *
     * @param int|string $id The Id of the entity we're searching for
     * @return object The entity with the input Id
     * @throws OrmException Thrown if there was no entity with the input Id
     */
    public function getById($id): object;
}
