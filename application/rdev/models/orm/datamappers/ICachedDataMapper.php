<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for data mappers whose data is cached
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models\ORM\Exceptions;

interface ICachedDataMapper extends IDataMapper
{
    /**
     * Performs any cache actions that have been scheduled
     * This is best used when committing an SQL data mapper via a unit of work, and then calling this method after
     * the commit successfully finishes
     *
     * @throws Exceptions\ORMException Thrown if there was an error synchronizing the cache
     */
    public function syncCache();
} 