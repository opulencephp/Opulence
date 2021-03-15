<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
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
    protected function getSetMembersFromRedis(string $key)
    {
        return $this->redis->smembers($key);
    }

    /**
     * @inheritdoc
     */
    protected function getSortedSetMembersFromRedis(string $key)
    {
        return $this->redis->zrange($key, 0, -1);
    }

    /**
     * @inheritdoc
     */
    protected function getValueFromRedis(string $key)
    {
        return $this->redis->get($key);
    }
}
