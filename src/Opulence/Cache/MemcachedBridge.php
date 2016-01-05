<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cache;

use Opulence\Memcached\Memcached;

/**
 * Defines the Memcached bridge
 */
class MemcachedBridge
{
    /** @var Memcached The Memcached driver */
    protected $memcached = null;
    /** @var string The name of the client to connect to */
    protected $clientName = "default";
    /** @var string The prefix to use on all keys */
    protected $keyPrefix = "";

    /**
     * @param Memcached $memcached The Memcached driver
     * @param string $clientName The name of the client to connect to
     * @param string $keyPrefix The prefix to use on all keys
     */
    public function __construct(Memcached $memcached, $clientName = "default", $keyPrefix = "")
    {
        $this->memcached = $memcached;
        $this->clientName = $clientName;
        $this->keyPrefix = $keyPrefix;
    }

    /**
     * @inheritdoc
     */
    public function decrement($key, $by = 1)
    {
        return $this->getClient()->decrement($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        $this->getClient()->delete($this->getPrefixedKey($key));
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->getClient()->flush();
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        $value = $this->getClient()->get($this->getPrefixedKey($key));

        return $this->getClient()->getResultCode() === 0 ? $value : null;
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
        return $this->getClient()->get($this->getPrefixedKey($key)) !== false;
    }

    /**
     * @inheritdoc
     */
    public function increment($key, $by = 1)
    {
        return $this->getClient()->increment($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $lifetime)
    {
        $this->getClient()->set($this->getPrefixedKey($key), $value, $lifetime);
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

    /**
     * Gets the selected client
     *
     * @return mixed The client
     */
    private function getClient()
    {
        return $this->memcached->getClient($this->clientName);
    }
}