<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for cache data mappers to implement
 */
namespace Opulence\ORM\DataMappers;
use Opulence\ORM\ORMException;

interface ICacheDataMapper extends IDataMapper
{
    /**
     * Flushes entities stored by this data mapper from cache
     *
     * @throws ORMException Thrown if the cache couldn't be flushed
     */
    public function flush();
} 