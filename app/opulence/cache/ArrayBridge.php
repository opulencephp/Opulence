<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the array bridge
 */
namespace Opulence\Cache;

class ArrayBridge implements ICacheBridge
{
    /** @var array The storage */
    private $storage = [];

    /**
     * @inheritdoc
     */
    public function decrement($key, $by = 1)
    {
        $this->storage[$key] -= $by;

        return $this->storage[$key];
    }

    /**
     * @inheritdoc
     */
    public function delete($key)
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
    public function get($key)
    {
        if(array_key_exists($key, $this->storage))
        {
            return $this->storage[$key];
        }

        return null;
    }

    /**
     * @inheritdoc
     */
    public function has($key)
    {
        return array_key_exists($key, $this->storage);
    }

    /**
     * @inheritdoc
     */
    public function increment($key, $by = 1)
    {
        $this->storage[$key] += $by;

        return $this->storage[$key];
    }

    /**
     * @inheritdoc
     */
    public function set($key, $value, $lifetime)
    {
        $this->storage[$key] = $value;
    }
}