<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Redis cache bridge
 */
namespace RDev\Cache;
use RDev\Redis\RDevPHPRedis;

class RedisBridge implements ICacheBridge
{
    /** @var RDevPHPRedis The Redis driver */
    protected $redis = null;

    /**
     * @param RDevPHPRedis $redis The Redis driver
     */
    public function __construct(RDevPHPRedis $redis)
    {
        $this->redis = $redis;
    }

    /**
     * {@inheritdoc}
     */
    public function decrement($key, $by = 1)
    {
        return $this->redis->decrBy($key, $by);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->redis->del($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->redis->flushAll();
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $value = $this->redis->get($key);

        return $value === false ? null : $value;
    }

    /**
     * {@inheritdoc}
     * @return RDevPHPRedis
     */
    public function getDriver()
    {
        return $this->redis;
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key, $by = 1)
    {
        return $this->redis->incrBy($key, $by);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->redis->set($key, $value);
    }
}