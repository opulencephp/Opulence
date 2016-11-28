<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Views\Caching;

use Opulence\Views\IView;

/**
 * Defines the view array cache, which is useful for testing
 */
class ArrayCache implements ICache
{
    /** @var array The storage for our views */
    private $storage = [];

    /**
     * @inheritdoc
     */
    public function flush()
    {
        $this->storage = [];
    }

    /**
     * @inheritdoc
     */
    public function gc()
    {
        // Don't do anything
    }

    /**
     * @inheritdoc
     */
    public function get(IView $view)
    {
        if (!$this->has($view)) {
            return null;
        }

        return $this->storage[$this->getViewKey($view)];
    }

    /**
     * @inheritdoc
     */
    public function has(IView $view) : bool
    {
        return isset($this->storage[$this->getViewKey($view)]);
    }

    /**
     * @inheritdoc
     */
    public function set(IView $view, string $compiledContents)
    {
        $this->storage[$this->getViewKey($view)] = $compiledContents;
    }

    /**
     * @inheritdoc
     */
    public function setGCChance(int $chance, int $divisor = 100)
    {
        // Don't do anything
    }

    /**
     * Gets key for the cached view
     *
     * @param IView $view The view whose cached file path we want
     * @return string The key for the cached view
     */
    private function getViewKey(IView $view) : string
    {
        return md5(http_build_query([
            "u" => $view->getContents(),
            "v" => $view->getVars()
        ]));
    }
}