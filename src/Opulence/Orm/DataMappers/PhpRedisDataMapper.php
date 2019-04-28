<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Orm\DataMappers;

/**
 * Defines the cache data mapper implemented by the PHPRedis library
 */
abstract class PhpRedisDataMapper extends RedisDataMapper
{
    /**
     * @inheritdoc
     */
    protected function getSetMembersFromRedis(string $key)
    {
        return $this->redis->sMembers($key);
    }

    /**
     * @inheritdoc
     */
    protected function getSortedSetMembersFromRedis(string $key)
    {
        return $this->redis->zRange($key, 0, -1);
    }

    /**
     * @inheritdoc
     */
    protected function getValueFromRedis(string $key)
    {
        return $this->redis->get($key);
    }
}
