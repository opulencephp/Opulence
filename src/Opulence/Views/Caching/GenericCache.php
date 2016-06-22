<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Caching;

use Opulence\Cache\ICacheBridge;
use Opulence\Views\IView;

/**
 * Defines a generic cache for compiled views, using an ICacheBridge
 * implementation as a storage back-end.
 */
class GenericCache implements ICache
{
    /** @var ICacheBridge A caching implementation */
    private $bridge;
    /** @var int The cache lifetime in seconds */
    private $lifetime;

    /**
     * @param ICacheBridge $bridge A caching implementation
     * @param int $lifetime The cache lifetime in seconds
     */
    public function __construct(ICacheBridge $bridge, int $lifetime)
    {
        $this->bridge = $bridge;
        $this->lifetime = $lifetime;
    }

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->bridge->flush();
    }

    /**
     * @inheritdoc
     */
    public function gc()
    {
        // Garbage collection is not needed with a cache bridge
    }

    /**
     * @inheritdoc
     */
    public function get(IView $view)
    {
        return $this->bridge->get($this->getKey($view));
    }

    /**
     * @inheritdoc
     */
    public function has(IView $view) : bool
    {
        return $this->bridge->has($this->getKey($view));
    }

    /**
     * @inheritdoc
     */
    public function set(IView $view, string $compiledContents)
    {
        $this->bridge->set($this->getKey($view), $compiledContents, $this->lifetime);
    }

    /**
     * @inheritdoc
     */
    public function setGCChance(int $chance, int $divisor = 100)
    {
        // Garbage collection is not needed with a cache bridge
    }

    /**
     * Gets key for cached view
     *
     * @param IView $view The view whose cache key we want
     * @return string The key for the cached view
     */
    private function getKey(IView $view) : string
    {
        return md5(http_build_query([
            "u" => $view->getContents(),
            "v" => $view->getVars()
        ]));
    }
}
