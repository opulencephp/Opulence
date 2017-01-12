<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Orm\DataMappers;

use Opulence\Databases\IConnection;
use Opulence\Memcached\Memcached;
use Opulence\Orm\Ids\Accessors\IIdAccessorRegistry;

/**
 * Defines the Memcached-cached SQL data mapper
 */
abstract class MemcachedCachedSqlDataMapper extends CachedSqlDataMapper
{
    /**
     * @param Memcached $cache The cache object used in the cache data mapper
     * @param IConnection $readConnection The read connection
     * @param IConnection $writeConnection The write connection
     * @param IIdAccessorRegistry $idAccessorRegistry The Id accessor registry
     */
    public function __construct(
        Memcached $cache,
        IConnection $readConnection,
        IConnection $writeConnection,
        IIdAccessorRegistry $idAccessorRegistry
    ) {
        parent::__construct($cache, $readConnection, $writeConnection, $idAccessorRegistry);
    }
}
