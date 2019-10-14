<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Cache;

use Redis;

/**
 * Defines the Redis cache bridge
 */
class RedisBridge implements ICacheBridge
{
    /** @var Redis The Redis driver */
    protected Redis $redis;
    /** @var string The prefix to use on all keys */
    protected string $keyPrefix;

    /**
     * @param Redis $redis The Redis driver
     * @param string $keyPrefix The prefix to use on all keys
     */
    public function __construct(Redis $redis, string $keyPrefix = '')
    {
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix;
    }

    /**
     * @inheritdoc
     */
    public function decrement(string $key, int $by = 1): int
    {
        return $this->redis->decrBy($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $key): void
    {
        $this->redis->del($this->getPrefixedKey($key));
    }

    /**
     * @inheritdoc
     */
    public function flush(): void
    {
        $this->redis->flushAll();
    }

    /**
     * @inheritdoc
     */
    public function get(string $key)
    {
        $value = $this->redis->get($this->getPrefixedKey($key));

        return $value === false ? null : $value;
    }

    /**
     * Gets the underlying Redis instance
     *
     * @return Redis The Redis instance
     */
    public function getRedis(): Redis
    {
        return $this->redis;
    }

    /**
     * @inheritdoc
     */
    public function has(string $key): bool
    {
        return $this->redis->get($this->getPrefixedKey($key)) !== false;
    }

    /**
     * @inheritdoc
     */
    public function increment(string $key, int $by = 1): int
    {
        return $this->redis->incrBy($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function set(string $key, $value, int $lifetime): void
    {
        $this->redis->setEx($this->getPrefixedKey($key), $lifetime, $value);
    }

    /**
     * Gets the key with the prefix
     *
     * @param string $key The key to prefix
     * @return string The prefixed key
     */
    protected function getPrefixedKey(string $key): string
    {
        return $this->keyPrefix . $key;
    }
}
