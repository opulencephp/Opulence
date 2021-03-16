<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Collections;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;
use RuntimeException;

/**
 * Defines the interface for immutable dictionaries to implement
 */
interface IImmutableDictionary extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Gets whether or not the key exists
     *
     * @param mixed $key The key to check for
     * @return bool True if the key exists, otherwise false
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function containsKey($key) : bool;

    /**
     * Gets whether or not the value exists in the hash table
     *
     * @param mixed $value The value to search for
     * @return bool True if the value exists, otherwise false
     */
    public function containsValue($value) : bool;

    /**
     * Gets the value of the key
     *
     * @param mixed $key The key to get
     * @return mixed The value if it was found, otherwise the default value
     * @throws OutOfBoundsException Thrown if the key could not be found
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function get($key);

    /**
     * Gets the list of keys in the dictionary
     *
     * @return array The list of keys in the dictionary
     */
    public function getKeys() : array;

    /**
     * Gets the list of values in the dictionary
     *
     * @return array The list of values in the dictionary
     */
    public function getValues() : array;

    /**
     * Gets all of the values as an array of key-value pairs
     *
     * @return array All of the values as a list of key-value pairs
     */
    public function toArray() : array;

    /**
     * Attempts to get the value at a key
     *
     * @param mixed $key The key to get
     * @param mixed $value The value of the key, if it exists
     * @return bool True if the key existed, otherwise false
     */
    public function tryGet($key, &$value) : bool;
}
