<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Collections;

use ArrayIterator;
use InvalidArgumentException;
use RuntimeException;
use Traversable;

/**
 * Defines a hash table
 */
class HashTable implements IDictionary
{
    /** @var KeyValuePair[] The list of values */
    protected $hashKeysToKvps = [];

    /**
     * @param KeyValuePair[] $kvps The list of values to add
     * @throws InvalidArgumentException Thrown if the array contains a non-key-value pair
     */
    public function __construct(array $kvps = [])
    {
        $this->addRange($kvps);
    }

    /**
     * @inheritdoc
     */
    public function add($key, $value) : void
    {
        $this->hashKeysToKvps[$this->getHashKey($key)] = new KeyValuePair($key, $value);
    }

    /**
     * @inheritdoc
     */
    public function addRange(array $kvps) : void
    {
        foreach ($kvps as $kvp) {
            if (!$kvp instanceof KeyValuePair) {
                throw new InvalidArgumentException('Value must be instance of ' . KeyValuePair::class);
            }

            $this->hashKeysToKvps[$this->getHashKey($kvp->getKey())] = $kvp;
        }
    }

    /**
     * @inheritdoc
     */
    public function clear() : void
    {
        $this->hashKeysToKvps = [];
    }

    /**
     * @inheritdoc
     */
    public function containsKey($key) : bool
    {
        return array_key_exists($this->getHashKey($key), $this->hashKeysToKvps);
    }

    /**
     * @inheritdoc
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
     * @inheritdoc
     */
    public function get($key, $default = null)
    {
        $hashKey = $this->getHashKey($key);

        return $this->containsKey($hashKey) ? $this->hashKeysToKvps[$hashKey]->getValue() : $default;
    }

    /**
     * @inheritdoc
     */
    public function getKeys() : array
    {
        $keys = [];

        foreach ($this->hashKeysToKvps as $hashKey => $kvp) {
            $keys[] = $kvp->getKey();
        }

        return $keys;
    }

    /**
     * @inheritdoc
     */
    public function getValues() : array
    {
        $values = [];

        foreach ($this->hashKeysToKvps as $hashKey => $kvp) {
            $values[] = $kvp->getValue();
        }

        return $values;
    }

    /**
     * @inheritdoc
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator(array_values($this->hashKeysToKvps));
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
     * @inheritdoc
     */
    public function removeKey($key) : void
    {
        unset($this->hashKeysToKvps[$this->getHashKey($key)]);
    }

    /**
     * @inheritdoc
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
