<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm;

use Opulence\Databases\IConnection;
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
    public function commit();

    /**
     * Detaches an entity from being managed
     *
     * @param object $entity The entity to detach
     */
    public function detach($entity);

    /**
     * Disposes of all data in this unit of work
     */
    public function dispose();

    /**
     * Gets the unit of work's entity registry
     *
     * @return IEntityRegistry The entity registry used by the unit of work
     */
    public function getEntityRegistry();

    /**
     * Registers a data mapper for a class
     * Registering a data mapper for a class will overwrite any previously-set data mapper for that class
     *
     * @param string $className The name of the class whose data mapper we're registering
     * @param IDataMapper $dataMapper The data mapper for the class
     */
    public function registerDataMapper($className, IDataMapper $dataMapper);

    /**
     * Schedules an entity for deletion
     *
     * @param object $entity The entity to schedule for deletion
     */
    public function scheduleForDeletion($entity);

    /**
     * Schedules an entity for insertion
     *
     * @param object $entity The entity to schedule for insertion
     */
    public function scheduleForInsertion($entity);

    /**
     * Schedules an entity for insertion
     *
     * @param object $entity The entity to schedule for insertion
     */
    public function scheduleForUpdate($entity);

    /**
     * Sets the database connection
     *
     * @param IConnection $connection The connection to use
     */
    public function setConnection(IConnection $connection);
}