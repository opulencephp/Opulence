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
use Throwable;
use Traversable;

/**
 * Defines a hash set
 */
class HashSet implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var array The set of values */
    protected $values = [];

    /**
     * @param array $values The set of values
     */
    public function __construct(array $values = [])
    {
        $this->addRange($values);
    }

    /**
     * Adds a value
     *
     * @param mixed $value The value to add
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function add($value) : void
    {
        $this->values[$this->getKey($value)] = $value;
    }

    /**
     * Adds a range of values
     *
     * @param array $values The values to add
     * @throws RuntimeException Thrown if the values' keys could not be calculated
     */
    public function addRange(array $values) : void
    {
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    /**
     * Clears all values from the set
     */
    public function clear() : void
    {
        $this->values = [];
    }

    /**
     * Gets whether or not the value exists
     *
     * @param mixed $value The value to search for
     * @return bool True if the value exists, otherwise false
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function containsValue($value) : bool
    {
        return isset($this->values[$this->getKey($value)]);
    }

    /**
     * @inheritdoc
     */
    public function count() : int
    {
        return count($this->values);
    }

    /**
     * @inheritdoc
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator(array_values($this->values));
    }

    /**
     * Intersects the values of the input array with the values already in the set
     *
     * @param array $values The values to intersect with
     * @throws RuntimeException Thrown if the values' keys could not be calculated
     */
    public function intersect(array $values) : void
    {
        $intersectedValues = [];

        // We don't use array_intersect because that does string comparisons, which requires __toString()
        foreach ($this->values as $key => $value) {
            if (in_array($value, $values, true)) {
                $intersectedValues[] = $value;
            }
        }

        $this->clear();
        $this->addRange($intersectedValues);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($index) : bool
    {
        throw new RuntimeException('Cannot use isset on set - use containsValue() instead');
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($index)
    {
        throw new RuntimeException('Cannot get a value from a set');
    }

    /**
     * @inheritdoc
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function offsetSet($index, $value) : void
    {
        $this->add($value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($index) : void
    {
        throw new RuntimeException('Cannot use unset on set');
    }

    /**
     * Removes a value from the set
     *
     * @param mixed $value The value to remove
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    public function removeValue($value) : void
    {
        unset($this->values[$this->getKey($value)]);
    }

    /**
     * Sorts the values of the set
     *
     * @param callable $comparer The comparer to sort with
     */
    public function sort(callable $comparer) : void
    {
        usort($this->values, $comparer);
    }

    /**
     * Gets all of the values as an array
     *
     * @return array All of the values
     */
    public function toArray() : array
    {
        return array_values($this->values);
    }

    /**
     * Unions the values of the input array with the values already in the set
     *
     * @param array $values The values to union with
     * @throws RuntimeException Thrown if the values' keys could not be calculated
     */
    public function union(array $values) : void
    {
        $unionedValues = array_merge(array_values($this->values), $values);
        $this->clear();
        $this->addRange($unionedValues);
    }

    /**
     * Gets the key for a value to use in the set
     *
     * @param mixed $value The value whose key we want
     * @return string The key for the value
     * @throws RuntimeException Thrown if the value's key could not be calculated
     */
    protected function getKey($value) : string
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
