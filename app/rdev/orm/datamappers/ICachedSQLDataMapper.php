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
     * @throws ORM\ORMException Thrown if there was an error refreshing the cache
     */
    public function refreshCache();
} 