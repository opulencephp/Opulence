<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Memcached-cached SQL data mapper
 */
namespace Opulence\ORM\DataMappers;

use Opulence\Databases\ConnectionPool;
use Opulence\Memcached\Memcached;

abstract class MemcachedCachedSQLDataMapper extends CachedSQLDataMapper
{
    /**
     * @param Memcached $cache The cache object used in the cache data mapper
     * @param ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     */
    public function __construct(Memcached $cache, ConnectionPool $connectionPool)
    {
        parent::__construct($cache, $connectionPool);
    }
} 