<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Memcached bridge
 */
namespace Opulence\Cache;
use Memcached;

class MemcachedBridge
{
    /** @var Memcached The Memcached driver */
    protected $memcached = null;
    /** @var string The prefix to use on all keys */
    protected $keyPrefix = "";

    /**
     * @param Memcached $memcached The Memcached driver
     * @param string $keyPrefix The prefix to use on all keys
     */
    public function __construct(Memcached $memcached, $keyPrefix = "")
    {
        $this->memcached = $memcached;
        $this->keyPrefix = $keyPrefix;
    }

    /**
     * @inheritdoc
     */
    public function decrement($key, $by = 1)
    {
        return $this->memcached->decrement($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        $this->memcached->delete($this->getPrefixedKey($key));
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->memcached->flush();
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        $value = $this->memcached->get($this->getPrefixedKey($key));

        return $this->memcached->getResultCode() === 0 ? $value : null;
    }

    /**
     * Gets the underlying Memcached instance
     *
     * @return Memcached The memcached instance
     */
    public function getMemcached()
    {
        return $this->memcached;
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        return $this->memcached->get($this->getPrefixedKey($key)) !== false;
    }

    /**
     * @inheritdoc
     */
    public function increment($key, $by = 1)
    {
        return $this->memcached->increment($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $lifetime)
    {
        $this->memcached->set($this->getPrefixedKey($key), $value, $lifetime);
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