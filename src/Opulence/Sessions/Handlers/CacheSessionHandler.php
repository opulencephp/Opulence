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
    public function destroy($sessionId) : bool
    {
        $this->cache->delete($sessionId);

        return true;
    }

    /**
     * @inheritdoc
     */
    public function gc($maxLifetime) : bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function open($savePath, $sessionId) : bool
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    protected function doRead(string $sessionId) : string
    {
        return $this->cache->get($sessionId);
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