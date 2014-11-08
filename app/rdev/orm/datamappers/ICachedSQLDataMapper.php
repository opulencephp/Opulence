<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for data mappers whose data is cached
 */
namespace RDev\ORM\DataMappers;
use RDev\ORM;

interface ICachedSQLDataMapper extends ISQLDataMapper
{
    /**
     * Performs any cache actions that have been scheduled
     * This is best used when committing an SQL data mapper via a unit of work, and then calling this method after
     * the commit successfully finishes
     *
     * @throws ORM\ORMException Thrown if there was an error committing to cache
     */
    public function commit();

    /**
     * Refreshes the data in cache with the data from the SQL data mapper
     *
     * @return ORM\IEntity[] The list of entities that were not already synced
     *      The "missing" list contains the entities that were not in cache
     *      The "differing" list contains the entities in cache that were not the same as SQL
     *      The "additional" list contains entities in cache that were not at all in SQL
     * @throws ORM\ORMException Thrown if there was an error refreshing the cache
     */
    public function refreshCache();

    /**
     * Refreshes an entity in cache with the entity from the SQL data mapper
     *
     * @param int|string $id The Id of the entity to sync
     * @throws ORM\ORMException Thrown if there was an error refreshing the entity
     */
    public function refreshEntity($id);
} 