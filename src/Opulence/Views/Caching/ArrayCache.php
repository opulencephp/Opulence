<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
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
    public function get(IView $view, bool $checkVars = false)
    {
        if (!$this->has($view)) {
            return null;
        }

        return $this->storage[$this->getViewKey($view, $checkVars)];
    }

    /**
     * @inheritdoc
     */
    public function has(IView $view, bool $checkVars = false) : bool
    {
        return isset($this->storage[$this->getViewKey($view, $checkVars)]);
    }

    /**
     * @inheritdoc
     */
    public function set(IView $view, string $compiledContents, bool $checkVars = false)
    {
        $this->storage[$this->getViewKey($view, $checkVars)] = $compiledContents;
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
     * @param bool $checkVars Whether or not we want to also check for variable value equivalence when looking up cached views
     * @return string The key for the cached view
     */
    private function getViewKey(IView $view, bool $checkVars) : string
    {
        $data = ['u' => $view->getContents()];

        if ($checkVars) {
            $data['v'] = $view->getVars();
        }

        return md5(http_build_query($data));
    }
}
