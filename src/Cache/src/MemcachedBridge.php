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

use Memcached;

/**
 * Defines the Memcached bridge
 */
class MemcachedBridge implements ICacheBridge
{
    /** @var Memcached The Memcached driver */
    protected Memcached $memcached;
    /** @var string The prefix to use on all keys */
    protected string $keyPrefix;

    /**
     * @param Memcached $memcached The Memcached driver
     * @param string $keyPrefix The prefix to use on all keys
     */
    public function __construct(Memcached $memcached, string $keyPrefix = '')
    {
        $this->memcached = $memcached;
        $this->keyPrefix = $keyPrefix;
    }

    /**
     * @inheritdoc
     */
    public function decrement(string $key, int $by = 1): int
    {
        return $this->memcached->decrement($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function delete(string $key): void
    {
        $this->memcached->delete($this->getPrefixedKey($key));
    }

    /**
     * @inheritdoc
     */
    public function flush(): void
    {
        $this->memcached->flush();
    }

    /**
     * @inheritdoc
     */
    public function get(string $key)
    {
        $value = $this->memcached->get($this->getPrefixedKey($key));

        return $this->memcached->getResultCode() === 0 ? $value : null;
    }

    /**
     * Gets the underlying Memcached instance
     *
     * @return Memcached The memcached instance
     */
    public function getMemcached(): Memcached
    {
        return $this->memcached;
    }

    /**
     * @inheritdoc
     */
    public function has(string $key): bool
    {
        return $this->memcached->get($this->getPrefixedKey($key)) !== false;
    }

    /**
     * @inheritdoc
     */
    public function increment(string $key, int $by = 1): int
    {
        return $this->memcached->increment($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function set(string $key, $value, int $lifetime): void
    {
        $this->memcached->set($this->getPrefixedKey($key), $value, $lifetime);
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
