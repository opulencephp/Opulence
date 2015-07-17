<?php
/** *
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for cache bridges to implement
 */
namespace Opulence\Cache;

interface ICacheBridge
{
    /**
     * Decrements a value at a given key
     *
     * @param string $key The key to decrement
     * @param int $by The amount to decrement by
     * @return int The new value
     */
    public function decrement($key, $by = 1);

    /**
     * Deletes the value at the input key
     *
     * @param string $key The key to delete
     */
    public function delete($key);

    /**
     * Flushes all of the data from cache
     */
    public function flush();

    /**
     * Gets the value stored at the input key
     *
     * @param string $key The key to get
     * @return mixed|null The value of the key if it exists, otherwise null
     */
    public function get($key);

    /**
     * Gets whether or not a key exists
     *
     * @param string $key True if the key exists, otherwise false
     * @return bool True if the key exists, otherwise false
     */
    public function has($key);

    /**
     * Increments a value at a given key
     *
     * @param string $key The key to increment
     * @param int $by The amount to increment by
     * @return int The new value
     */
    public function increment($key, $by = 1);

    /**
     * Sets a value at the input key
     *
     * @param string $key The key to set
     * @param mixed $value The value to setICacheBridge
     * @param int $lifetime The number of seconds to live in cache
     */
    public function set($key, $value, $lifetime);
}