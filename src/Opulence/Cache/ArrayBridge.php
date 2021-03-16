<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Cache;

/**
 * Defines the array bridge
 */
class ArrayBridge implements ICacheBridge
{
    /** @var array The storage */
    private $storage = [];

    /**
     * @inheritdoc
     */
    public function decrement(string $key, int $by = 1) : int
    {
        $this->storage[$key] -= $by;

        return $this->storage[$key];
    }

    /**
     * @inheritdoc
     */
    public function delete(string $key)
    {
        unset($this->storage[$key]);
    }

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
    public function get(string $key)
    {
        if (array_key_exists($key, $this->storage)) {
            return $this->storage[$key];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function has(string $key) : bool
    {
        return array_key_exists($key, $this->storage);
    }

    /**
     * @inheritdoc
     */
    public function increment(string $key, int $by = 1) : int
    {
        $this->storage[$key] += $by;

        return $this->storage[$key];
    }

    /**
     * @inheritdoc
     */
    public function set(string $key, $value, int $lifetime)
    {
        if ($lifetime > 0) {
            $this->storage[$key] = $value;
        }
    }
}
