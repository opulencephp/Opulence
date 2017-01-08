<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Views\Caching;

use Opulence\Cache\ICacheBridge;
use Opulence\Views\Caching\ICache;
use Opulence\Views\IView;

/**
 * Defines an cache bridge implementation of a compiled view cache as a storage back-end
 */
class GenericCache implements ICache
{
    /** @var ICacheBridge A caching implementation */
    private $bridge = null;
    /** @var int The cache lifetime in seconds */
    private $lifetime = -1;

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
    public function get(IView $view, bool $checkVars = false)
    {
        return $this->bridge->get($this->getKey($view, $checkVars));
    }

    /**
     * @inheritdoc
     */
    public function has(IView $view, bool $checkVars = false) : bool
    {
        return $this->bridge->has($this->getKey($view, $checkVars));
    }

    /**
     * @inheritdoc
     */
    public function set(IView $view, string $compiledContents, bool $checkVars = false)
    {
        $this->bridge->set($this->getKey($view, $checkVars), $compiledContents, $this->lifetime);
    }

    /**
     * @inheritdoc
     */
    public function setGCChance(int $chance, int $divisor = 100)
    {
        // Garbage collection is not needed with a cache bridge
    }

    /**
     * Gets the key for the cached view
     *
     * @param IView $view The view whose cache key we want
     * @param bool $checkVars Whether or not we want to also check for variable value equivalence when looking up cached views
     * @return string The key for the cached view
     */
    private function getKey(IView $view, bool $checkVars) : string
    {
        $data = ["u" => $view->getContents()];

        if ($checkVars) {
            $data["v"] = $view->getVars();
        }

        return md5(http_build_query($data));
    }
}
