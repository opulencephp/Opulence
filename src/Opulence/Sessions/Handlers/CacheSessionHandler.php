<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Sessions\Handlers;

use Opulence\Cache\ICacheBridge;

/**
 * Defines the cache session handler
 */
class CacheSessionHandler extends SessionHandler
{
    /** @var ICacheBridge The cache to use */
    private $cache = null;
    /** @var int The lifetime in seconds */
    private $lifetime = 0;

    /**
     * @param ICacheBridge $cache The cache to use
     * @param int $lifetime The lifetime in seconds
     */
    public function __construct(ICacheBridge $cache, int $lifetime)
    {
        $this->cache = $cache;
        $this->lifetime = $lifetime;
    }

    /**
     * @inheritdoc
     */
    public function close() : bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroy($id) : bool
    {
        $this->cache->delete($id);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function gc($max_lifetime) : int
    {
        return 0;
    }

    /**
     * @inheritdoc
     */
    public function open($path, $name) : bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doRead(string $sessionId) : string
    {
        return $this->cache->get($sessionId) ?? '';
    }

    /**
     * @inheritdoc
     */
    protected function doWrite(string $sessionId, string $sessionData) : bool
    {
        $this->cache->set($sessionId, $sessionData, $this->lifetime);

        return true;
    }
}
