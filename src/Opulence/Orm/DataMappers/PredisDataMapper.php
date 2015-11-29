<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Orm\DataMappers;

/**
 * Defines the cache data mapper implemented by the Predis library
 */
abstract class PredisDataMapper extends RedisDataMapper
{
    /**
     * @inheritdoc
     */
    protected function getSetMembersFromRedis($key)
    {
        return $this->redis->smembers($key);
    }

    /**
     * @inheritdoc
     */
    protected function getSortedSetMembersFromRedis($key)
    {
        return $this->redis->zrange($key, 0, -1);
    }

    /**
     * @inheritdoc
     */
    protected function getValueFromRedis($key)
    {
        return $this->redis->get($key);
    }
} 