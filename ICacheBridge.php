<?php
/** *
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for cache bridges to implement
 */
namespace RDev\Cache;

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
     * Gets the driver used by the bridge
     *
     * @return mixed The driver used by the bridge
     */
    public function getDriver();

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
     * @param mixed $value The value to set
     */
    public function set($key, $value);
}