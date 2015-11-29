<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm\DataMappers;

use Opulence\Databases\IConnection;
use Opulence\Orm\Ids\Accessors\IIdAccessorRegistry;
use Opulence\Redis\Redis;

/**
 * Defines the Redis-cached SQL data mapper
 */
abstract class RedisCachedSqlDataMapper extends CachedSqlDataMapper
{
    /**
     * @param Redis $cache The cache object used in the cache data mapper
     * @param IConnection $readConnection The read connection
     * @param IConnection $writeConnection The write connection
     * @param IIdAccessorRegistry $idAccessorRegistry The Id accessor registry
     */
    public function __construct(
        Redis $cache,
        IConnection $readConnection,
        IConnection $writeConnection,
        IIdAccessorRegistry $idAccessorRegistry
    ) {
        parent::__construct($cache, $readConnection, $writeConnection, $idAccessorRegistry);
    }
} 