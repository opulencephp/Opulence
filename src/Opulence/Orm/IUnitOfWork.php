<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm;

use Opulence\Orm\DataMappers\IDataMapper;

/**
 * Defines the interface for units of work to implement
 */
interface IUnitOfWork
{
    /**
     * Commits any entities that have been scheduled for insertion/updating/deletion
     *
     * @throws OrmException Thrown if there was an error committing the transaction
     */
    public function commit(): void;

    /**
     * Detaches an entity from being managed
     *
     * @param object $entity The entity to detach
     */
    public function detach(object $entity): void;

    /**
     * Disposes of all data in this unit of work
     */
    public function dispose(): void;

    /**
     * Gets the unit of work's entity registry
     *
     * @return IEntityRegistry The entity registry used by the unit of work
     */
    public function getEntityRegistry(): IEntityRegistry;

    /**
     * Registers a data mapper for a class
     * Registering a data mapper for a class will overwrite any previously-set data mapper for that class
     *
     * @param string $className The name of the class whose data mapper we're registering
     * @param IDataMapper $dataMapper The data mapper for the class
     */
    public function registerDataMapper(string $className, IDataMapper $dataMapper): void;

    /**
     * Schedules an entity for deletion
     *
     * @param object $entity The entity to schedule for deletion
     */
    public function scheduleForDeletion(object $entity): void;

    /**
     * Schedules an entity for insertion
     *
     * @param object $entity The entity to schedule for insertion
     */
    public function scheduleForInsertion(object $entity): void;

    /**
     * Schedules an entity for insertion
     *
     * @param object $entity The entity to schedule for insertion
     */
    public function scheduleForUpdate(object $entity): void;
}
