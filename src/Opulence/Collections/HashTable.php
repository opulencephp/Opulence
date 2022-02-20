<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Collections;

use ArrayIterator;
use InvalidArgumentException;
use OutOfBoundsException;
use RuntimeException;
use Traversable;

/**
 * Defines a hash table
 */
class HashTable implements IDictionary
{
    /** @var KeyValuePair[] The list of values */
    protected $hashKeysToKvps = [];
    /** @var KeyHasher The key hasher to use */
    private $keyHasher = null;

    /**
     * @param KeyValuePair[] $kvps The list of key-value pairs to add
     * @throws InvalidArgumentException Thrown if the array contains a non-key-value pair
     */
    public function __construct(array $kvps = [])
    {
        $this->keyHasher = new KeyHasher();
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
        foreach ($this->hashKeysToKvps as $kvp) {
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
    public function get($key)
    {
        $hashKey = $this->getHashKey($key);

        if (!$this->containsKey($key)) {
            throw new OutOfBoundsException("Hash key \"$hashKey\" not found");
        }

        return $this->hashKeysToKvps[$hashKey]->getValue();
    }

    /**
     * @inheritdoc
     */
    public function getKeys() : array
    {
        $keys = [];

        foreach ($this->hashKeysToKvps as $kvp) {
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

        foreach ($this->hashKeysToKvps as $kvp) {
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
    public function offsetExists($offset) : bool
    {
        return $this->containsKey($offset);
    }

    /**
     * @inheritdoc
     * @throws OutOfBoundsException Thrown if the key could not be found
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    /**
     * @inheritdoc
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function offsetSet($offset, $value) : void
    {
        $this->add($offset, $value);
    }

    /**
     * @inheritdoc
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function offsetUnset($offset) : void
    {
        $this->removeKey($offset);
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
     * @inheritdoc
     */
    public function tryGet($key, &$value) : bool
    {
        try {
            $value = $this->get($key);

            return true;
        } catch (OutOfBoundsException $ex) {
            return false;
        }

        return false;
    }

    /**
     * Gets the hash key for a value
     * This method allows extending classes to customize how hash keys are calculated
     *
     * @param string|int|float|array|object|resource $value The value whose hash key we want
     * @return string The hash key
     * @throws RuntimeException Thrown if the hash key could not be calculated
     */
    protected function getHashKey($value) : string
    {
        return $this->keyHasher->getHashKey($value);
    }
}
