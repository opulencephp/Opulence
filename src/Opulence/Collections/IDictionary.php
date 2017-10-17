<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections;

use ArrayAccess;
use Countable;
use IteratorAggregate;
use OutOfBoundsException;
use RuntimeException;

/**
 * Defines the interface for dictionaries to implement
 */
interface IDictionary extends ArrayAccess, Countable, IteratorAggregate
{
    /**
     * Adds a value
     *
     * @param mixed $key The key to add
     * @param mixed $value The value to add
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function add($key, $value) : void;

    /**
     * Adds multiple values
     *
     * @param array $values The values to add
     * @throws RuntimeException Thrown if the values' keys could not be calculated
     */
    public function addRange(array $values) : void;

    /**
     * Clears all values from the dictionary
     */
    public function clear() : void;

    /**
     * Gets whether or not the key exists
     *
     * @param mixed $key The key to check for
     * @return bool True if the key exists, otherwise false
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function containsKey($key) : bool;

    /**
     * Gets whether or not the value exists in the dictionary
     *
     * @param mixed $value The value to search for
     * @return bool True if the value exists, otherwise false
     */
    public function containsValue($value) : bool;

    /**
     * Gets the value of the key
     *
     * @param mixed $key The key to get
     * @return mixed The value at the key
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
     * Removes a key
     *
     * @param mixed $key The key to remove
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function removeKey($key) : void;

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
