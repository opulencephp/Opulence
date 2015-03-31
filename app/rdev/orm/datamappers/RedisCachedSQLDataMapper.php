<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Redis-cached SQL data mapper
 */
namespace RDev\ORM\DataMappers;
use RDev\Databases\NoSQL\Redis\IRedis;
use RDev\Databases\SQL\ConnectionPool;

abstract class RedisCachedSQLDataMapper extends CachedSQLDataMapper
{
    /**
     * @param IRedis $cache The cache object used in the cache data mapper
     * @param ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     */
    public function __construct(IRedis $cache, ConnectionPool $connectionPool)
    {
        parent::__construct($cache, $connectionPool);
    }
} 