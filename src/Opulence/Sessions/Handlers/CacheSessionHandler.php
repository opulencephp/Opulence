<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
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
    public function __construct(ICacheBridge $cache, $lifetime)
    {
        $this->cache = $cache;
        $this->lifetime = $lifetime;
    }

    /**
     * @inheritdoc
     */
    public function close()
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function destroy($sessionId)
    {
        $this->cache->delete($sessionId);
    }

    /**
     * @inheritdoc
     */
    public function gc($maxLifetime)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function open($savePath, $sessionId)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doRead($sessionId)
    {
        return $this->cache->get($sessionId);
    }

    /**
     * @inheritdoc
     */
    protected function doWrite($sessionId, $sessionData)
    {
        $this->cache->set($sessionId, $sessionData, $this->lifetime);
    }
}