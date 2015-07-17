<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for data mappers whose data is cached
 */
namespace Opulence\ORM\DataMappers;
use Opulence\ORM\IEntity;
use Opulence\ORM\ORMException;

interface ICachedSQLDataMapper extends ISQLDataMapper
{
    /**
     * Performs any cache actions that have been scheduled
     * This is best used when committing an SQL data mapper via a unit of work, and then calling this method after
     * the commit successfully finishes
     *
     * @throws ORMException Thrown if there was an error committing to cache
     */
    public function commit();

    /**
     * Gets the cache data mapper
     *
     * @return ICacheDataMapper The cache data mapper
     */
    public function getCacheDataMapper();

    /**
     * Gets the SQL data mapper
     *
     * @return ISQLDataMapper The SQL data mapper
     */
    public function getSQLDataMapper();

    /**
     * Gets a list of entities that differ in cache and the SQL database
     *
     * @return IEntity[] The list of entities that were not already synced
     *      The "missing" list contains the entities that were not in cache
     *      The "differing" list contains the entities in cache that were not the same as SQL
     *      The "additional" list contains entities in cache that were not at all in SQL
     * @throws ORMException Thrown if there was an error getting the unsynced entities
     */
    public function getUnsyncedEntities();

    /**
     * Refreshes the data in cache with the data from the SQL data mapper
     *
     * @return IEntity[] The list of entities that were not already synced
     *      The "missing" list contains the entities that were not in cache
     *      The "differing" list contains the entities in cache that were not the same as SQL
     *      The "additional" list contains entities in cache that were not at all in SQL
     * @throws ORMException Thrown if there was an error refreshing the cache
     */
    public function refreshCache();

    /**
     * Refreshes an entity in cache with the entity from the SQL data mapper
     *
     * @param int|string $id The Id of the entity to sync
     * @throws ORMException Thrown if there was an error refreshing the entity
     */
    public function refreshEntity($id);
} 