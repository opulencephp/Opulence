<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Memcached-cached SQL data mapper
 */
namespace RDev\ORM\DataMappers;
use RDev\Databases\NoSQL\Memcached\RDevMemcached;
use RDev\Databases\SQL\ConnectionPool;

abstract class MemcachedCachedSQLDataMapper extends CachedSQLDataMapper
{
    /**
     * @param RDevMemcached $cache The cache object used in the cache data mapper
     * @param ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     */
    public function __construct(RDevMemcached $cache, ConnectionPool $connectionPool)
    {
        parent::__construct($cache, $connectionPool);
    }
} 