<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for cache data mappers to implement
 */
namespace RDev\ORM\DataMappers;
use RDev\ORM;

interface ICacheDataMapper extends IDataMapper
{
    /**
     * Flushes entities stored by this data mapper from cache
     *
     * @throws ORM\ORMException Thrown if the cache couldn't be flushed
     */
    public function flush();
} 