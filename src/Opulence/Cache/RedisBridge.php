<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cache;

use Opulence\Redis\Redis;

/**
 * Defines the Redis cache bridge
 */
class RedisBridge implements ICacheBridge
{
    /** @var Redis The Redis driver */
    protected $redis = null;
    /** @var string The name of the client to connect to */
    protected $clientName = "default";
    /** @var string The prefix to use on all keys */
    protected $keyPrefix = "";

    /**
     * @param Redis $redis The Redis driver
     * @param string $clientName The name of the client to connect to
     * @param string $keyPrefix The prefix to use on all keys
     */
    public function __construct(Redis $redis, $clientName = "default", $keyPrefix = "")
    {
        $this->redis = $redis;
        $this->clientName = $clientName;
        $this->keyPrefix = $keyPrefix;
    }

    /**
     * @inheritdoc
     */
    public function decrement($key, $by = 1)
    {
        return $this->getClient()->decrBy($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
    {
        $this->getClient()->del($this->getPrefixedKey($key));
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->getClient()->flushAll();
    }

    /**
     * @inheritdoc
     */
    public function get($key)
    {
        $value = $this->getClient()->get($this->getPrefixedKey($key));

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
        return $this->getClient()->get($this->getPrefixedKey($key)) !== false;
    }

    /**
     * @inheritdoc
     */
    public function increment($key, $by = 1)
    {
        return $this->getClient()->incrBy($this->getPrefixedKey($key), $by);
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $lifetime)
    {
        $this->getClient()->setEx($this->getPrefixedKey($key), $value, $lifetime);
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
        return $this->redis->getClient($this->clientName);
    }
}