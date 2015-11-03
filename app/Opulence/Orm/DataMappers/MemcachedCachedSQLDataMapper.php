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

abstract class MemcachedCachedSqlDataMapper extends CachedSqlDataMapper
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