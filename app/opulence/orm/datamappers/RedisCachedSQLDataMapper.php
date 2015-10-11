<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Redis-cached SQL data mapper
 */
namespace Opulence\ORM\DataMappers;

use Opulence\Databases\ConnectionPool;
use Opulence\Redis\Redis;

abstract class RedisCachedSQLDataMapper extends CachedSQLDataMapper
{
    /**
     * @param Redis $cache The cache object used in the cache data mapper
     * @param ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     */
    public function __construct(Redis $cache, ConnectionPool $connectionPool)
    {
        parent::__construct($cache, $connectionPool);
    }
} 