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
use ArrayIterator;
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Defines a hash table
 */
class HashTable implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var array The list of values */
    protected $values = [];

    /**
     * @param array $values The list of values
     */
    public function __construct(array $values = [])
    {
        $this->addRange($values);
    }

    /**
     * Adds a value
     *
     * @param string $key The key to add
     * @param mixed $value The value to add
     */
    public function add(string $key, $value) : void
    {
        $this->values[$key] = $value;
    }

    /**
     * Adds multiple values
     *
     * @param array $values The values to add
     */
    public function addRange(array $values) : void
    {
        foreach ($values as $key => $value) {
            $this->add($key, $value);
        }
    }

    /**
     * Clears all values from the hash table
     */
    public function clear() : void
    {
        $this->values = [];
    }

    /**
     * Gets whether or not the key exists
     *
     * @param string $key The key to check for
     * @return bool True if the key exists, otherwise false
     */
    public function containsKey(string $key) : bool
    {
        return array_key_exists($key, $this->values);
    }

    /**
     * Gets whether or not the value exists in the hash table
     *
     * @param mixed $value The value to search for
     * @return bool True if the value exists, otherwise false
     */
    public function containsValue($value) : bool
    {
        return array_search($value, $this->values) !== false;
    }

    /**
     * @inheritdoc
     */
    public function count() : int
    {
        return count($this->values);
    }

    /**
     * Gets the value of the key
     *
     * @param string $key The key to get
     * @param mixed $default The default value
     * @return mixed The value if it was found, otherwise the default value
     */
    public function get(string $key, $default = null)
    {
        return $this->containsKey($key) ? $this->values[$key] : $default;
    }

    /**
     * @inheritdoc
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->values);
    }

    /**
     * Intersects the values of the input array with the values already in the hash table
     * Keys and values are compared when intersecting
     *
     * @param array $values The values to intersect with
     */
    public function intersect(array $values) : void
    {
        $intersectedValues = array_intersect_assoc($this->values, $values);
        $this->clear();
        $this->addRange($intersectedValues);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($key) : bool
    {
        return $this->containsKey($key);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($key)
    {
        return $this->get($key, null);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($key, $value) : void
    {
        $this->add($key, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset) : void
    {
        $this->removeKey($offset);
    }

    /**
     * Removes a key
     *
     * @param string $key The key to remove
     */
    public function removeKey(string $key) : void
    {
        unset($this->values[$key]);
    }

    /**
     * Gets all of the values as an array
     *
     * @return array All of the values
     */
    public function toArray() : array
    {
        return $this->values;
    }

    /**
     * Unions the values of the input array with the values already in the hash table
     * If the hash table and array have the same key, the value from the array will be used
     *
     * @param array $values The values to union with
     */
    public function union(array $values) : void
    {
        $unionedValues = array_merge(($this->values), $values);
        $this->clear();
        $this->addRange($unionedValues);
    }
}
