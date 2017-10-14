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
use RuntimeException;
use Traversable;

/**
 * Defines a hash table
 */
class HashTable implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var KeyValuePair[] The list of values */
    protected $hashKeysToKvps = [];

    /**
     * @param array $values The list of values to add
     */
    public function __construct(array $values = [])
    {
        $this->addRange($values);
    }

    /**
     * Adds a value
     *
     * @param mixed $key The key to add
     * @param mixed $value The value to add
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function add($key, $value) : void
    {
        $this->hashKeysToKvps[$this->getHashKey($key)] = new KeyValuePair($key, $value);
    }

    /**
     * Adds multiple values
     *
     * @param array $values The values to add
     * @throws RuntimeException Thrown if the values' keys could not be calculated
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
        $this->hashKeysToKvps = [];
    }

    /**
     * Gets whether or not the key exists
     *
     * @param mixed $key The key to check for
     * @return bool True if the key exists, otherwise false
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function containsKey($key) : bool
    {
        return array_key_exists($this->getHashKey($key), $this->hashKeysToKvps);
    }

    /**
     * Gets whether or not the value exists in the hash table
     *
     * @param mixed $value The value to search for
     * @return bool True if the value exists, otherwise false
     */
    public function containsValue($value) : bool
    {
        foreach ($this->hashKeysToKvps as $hashKey => $kvp) {
            if ($kvp->getValue() == $value) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    public function count() : int
    {
        return count($this->hashKeysToKvps);
    }

    /**
     * Gets the value of the key
     *
     * @param mixed $key The key to get
     * @param mixed $default The default value
     * @return mixed The value if it was found, otherwise the default value
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function get($key, $default = null)
    {
        $hashKey = $this->getHashKey($key);

        return $this->containsKey($hashKey) ? $this->hashKeysToKvps[$hashKey]->getValue() : $default;
    }

    /**
     * @inheritdoc
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->hashKeysToKvps);
    }

    /**
     * @inheritdoc
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function offsetExists($key) : bool
    {
        return $this->containsKey($key);
    }

    /**
     * @inheritdoc
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function offsetGet($key)
    {
        return $this->get($key, null);
    }

    /**
     * @inheritdoc
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function offsetSet($key, $value) : void
    {
        $this->add($key, $value);
    }

    /**
     * @inheritdoc
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function offsetUnset($key) : void
    {
        $this->removeKey($key);
    }

    /**
     * Removes a key
     *
     * @param mixed $key The key to remove
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function removeKey($key) : void
    {
        unset($this->hashKeysToKvps[$this->getHashKey($key)]);
    }

    /**
     * Gets all of the values as an array of key-value pairs
     *
     * @return array All of the values as a list of key-value pairs
     */
    public function toArray() : array
    {
        return array_values($this->hashKeysToKvps);
    }

    /**
     * Gets the key for a value to use in the hash table
     *
     * @param mixed $value The value whose key we want
     * @return string The key for the value
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    protected function getHashKey($value) : string
    {
        if (is_object($value)) {
            return spl_object_hash($value);
        }

        if (is_array($value)) {
            return md5(serialize($value));
        }

        if (is_resource($value)) {
            return "$value";
        }

        try {
            return (string)$value;
        } catch (Throwable $ex) {
            throw new RuntimeException('Value could not be converted to a key', 0, $ex);
        }
    }
}
