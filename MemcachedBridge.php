<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Memcached bridge
 */
namespace RDev\Cache;
use RDev\Memcached\RDevMemcached;

class MemcachedBridge
{
    /** @var RDevMemcached The Memcached driver */
    protected $memcached = null;

    /**
     * @param RDevMemcached $memcached The Memcached driver
     */
    public function __construct(RDevMemcached $memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * {@inheritdoc}
     */
    public function decrement($key, $by = 1)
    {
        return $this->memcached->decrement($key, $by);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($key)
    {
        $this->memcached->delete($key);
    }

    /**
     * {@inheritdoc}
     */
    public function flush()
    {
        $this->memcached->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function get($key)
    {
        $value = $this->memcached->get($key);

        return $this->memcached->getResultCode() === 0 ? $value : null;
    }

    /**
     * {@inheritdoc}
     * @return RDevMemcached
     */
    public function getDriver()
    {
        return $this->memcached;
    }

    /**
     * {@inheritdoc}
     */
    public function increment($key, $by = 1)
    {
        return $this->memcached->increment($key, $by);
    }

    /**
     * {@inheritdoc}
     */
    public function set($key, $value)
    {
        $this->memcached->set($key, $value);
    }
}