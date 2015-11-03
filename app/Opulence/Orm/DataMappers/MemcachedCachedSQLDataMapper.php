<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
/**
 * Defines the Memcached-cached SQL data mapper
 */
namespace Opulence\Orm\DataMappers;

use Opulence\Databases\ConnectionPools\ConnectionPool;
use Opulence\Memcached\Memcached;
use Opulence\Orm\Ids\IIdAccessorRegistry;

abstract class MemcachedCachedSqlDataMapper extends CachedSqlDataMapper
{
    /**
     * @param Memcached $cache The cache object used in the cache data mapper
     * @param ConnectionPool $connectionPool The connection pool used in the SQL data mapper
     * @param IIdAccessorRegistry $idAccessorRegistry The Id accessor registry
     */
    public function __construct(
        Memcached $cache,
        ConnectionPool $connectionPool,
        IIdAccessorRegistry $idAccessorRegistry
    ) {
        parent::__construct($cache, $connectionPool, $idAccessorRegistry);
    }
} 