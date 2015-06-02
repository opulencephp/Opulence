<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the cache session handler
 */
namespace RDev\Sessions\Handlers;
use RDev\Cache\ICacheBridge;
use SessionHandlerInterface;

class CacheSessionHandler implements SessionHandlerInterface
{
    /** @var ICacheBridge The cache to use */
    private $cache = null;
    /** @var int The lifetime in seconds */
    private $lifetime = 0;

    /**
     * @param ICacheBridge $cache The cache to use
     * @param int $lifetime The lifetime in seconds
     */
    public function __construct(ICacheBridge $cache, $lifetime)
    {
        $this->cache = $cache;
        $this->lifetime = $lifetime;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $this->cache->delete($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxLifetime)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($sessionId)
    {
        return $this->cache->get($sessionId);
    }

    /**
     * {@inheritdoc}
     */
    public function write($sessionId, $sessionData)
    {
        $this->cache->set($sessionId, $sessionData, $this->lifetime);
    }
}