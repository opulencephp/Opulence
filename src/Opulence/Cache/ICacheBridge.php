<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cache;

/** *
 * Defines the interface for cache bridges to implement
 */
interface ICacheBridge
{
    /**
     * Decrements a value at a given key
     *
     * @param string $key The key to decrement
     * @param int $by The amount to decrement by
     * @return int The new value
     */
    public function decrement(string $key, int $by = 1) : int;

    /**
     * Deletes the value at the input key
     *
     * @param string $key The key to delete
     */
    public function delete(string $key);

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
    public function get(string $key);

    /**
     * Gets whether or not a key exists
     *
     * @param string $key True if the key exists, otherwise false
     * @return bool True if the key exists, otherwise false
     */
    public function has(string $key) : bool;

    /**
     * Increments a value at a given key
     *
     * @param string $key The key to increment
     * @param int $by The amount to increment by
     * @return int The new value
     */
    public function increment(string $key, int $by = 1) : int;

    /**
     * Sets a value at the input key
     *
     * @param string $key The key to set
     * @param mixed $value The value to setICacheBridge
     * @param int $lifetime The number of seconds to live in cache
     */
    public function set(string $key, $value, int $lifetime);
}