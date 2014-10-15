<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the Redis-cached SQL data mapper
 */
namespace RDev\Models\ORM\DataMappers;
use RDev\Models\Databases\NoSQL\Redis;
use RDev\Models\Databases\SQL;

abstract class RedisCachedSQLDataMapper extends CachedSQLDataMapper
{
    /**
     * @param Redis\IRedis $cache The cache object used in the cache data mapper
     * @param SQL\ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     */
    public function __construct(Redis\IRedis $cache, SQL\ConnectionPool $connectionPool)
    {
        parent::__construct($cache, $connectionPool);
    }
} 