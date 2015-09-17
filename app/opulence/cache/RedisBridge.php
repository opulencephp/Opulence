<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Redis cache bridge
 */
namespace Opulence\Cache;
use Redis;

class RedisBridge implements ICacheBridge
{
    /** @var Redis The Redis driver */
    protected $redis = null;
    /** @var string The prefix to use on all keys */
    protected $keyPrefix = "";

    /**
     * @param Redis $redis The Redis driver
     * @param string $keyPrefix The prefix to use on all keys
     */
    public function __construct(Redis $redis, $keyPrefix = "")
    {
        $this->redis = $redis;
        $this->keyPrefix = $keyPrefix;
    }

    /**
     * @inheritdoc
     */
    public function decrement($key, $by = 1)
    {
        return $this->redis->decrBy($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        $this->redis->del($this->getPrefixedKey($key));
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->redis->flushAll();
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        $value = $this->redis->get($this->getPrefixedKey($key));

        return $value === false ? null : $value;
    }

    /**
     * Gets the underlying Redis instance
     *
     * @return Redis The Redis instance
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        return $this->redis->get($this->getPrefixedKey($key)) !== false;
    }

    /**
     * @inheritdoc
     */
    public function increment($key, $by = 1)
    {
        return $this->redis->incrBy($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $lifetime)
    {
        $this->redis->setEx($this->getPrefixedKey($key), $value, $lifetime);
    }

    /**
     * Gets the key with the prefix
     *
     * @param string $key The key to prefix
     * @return string The prefixed key
     */
    protected function getPrefixedKey($key)
    {
        return $this->keyPrefix . $key;
    }
}