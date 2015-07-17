<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Memcached-cached SQL data mapper
 */
namespace Opulence\ORM\DataMappers;
use Opulence\Memcached\OpulenceMemcached;
use Opulence\Databases\ConnectionPool;

abstract class MemcachedCachedSQLDataMapper extends CachedSQLDataMapper
{
    /**
     * @param OpulenceMemcached $cache The cache object used in the cache data mapper
     * @param ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     */
    public function __construct(OpulenceMemcached $cache, ConnectionPool $connectionPool)
    {
        parent::__construct($cache, $connectionPool);
    }
} 