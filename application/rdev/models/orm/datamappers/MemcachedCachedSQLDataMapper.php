<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the Memcached-cached SQL data mapper
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models\Databases\NoSQL\Memcached;
use RDev\Models\Databases\SQL;

abstract class MemcachedCachedSQLDataMapper extends CachedSQLDataMapper
{
    /**
     * @param Memcached\RDevMemcached $cache The cache object used in the cache data mapper
     * @param SQL\ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     */
    public function __construct(Memcached\RDevMemcached $cache, SQL\ConnectionPool $connectionPool)
    {
        parent::__construct($cache, $connectionPool);
    }
} 