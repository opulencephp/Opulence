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
 * Defines an array list
 */
class ArrayList implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var array The list of values */
    protected $values = [];

    /**
     * @param array $values The list of values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    /**
     * Adds a value
     *
     * @param mixed $value The value to add
     */
    public function add($value) : void
    {
        $this->values[] = $value;
    }

    /**
     * Adds a range of values
     *
     * @param array $values The values to add
     */
    public function addRange($values) : void
    {
        foreach ($values as $value) {
            $this->add($value);
        }
    }

    /**
     * Clears all values from the list
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
     */
    public function containsValue($value) : bool
    {
        return $this->indexOf($value) !== null;
    }

    /**
     * @inheritdoc
     */
    public function count() : int
    {
        return count($this->values);
    }

    /**
     * Exchanges the current values with the input
     *
     * @param mixed $array The values to exchange with
     * @return array The old array
     */
    public function exchangeArray($array) : array
    {
        $oldValues = $this->values;
        $this->values = $array;

        return $oldValues;
    }

    /**
     * Gets the value at an index
     *
     * @param int $index The index to get
     * @param mixed $default The default value
     * @return mixed The value if it was found, otherwise the default value
     */
    public function get(int $index, $default = null)
    {
        return $this->values[$index] ?? $default;
    }

    /**
     * @inheritdoc
     */
    public function getIterator() : Traversable
    {
        return new ArrayIterator($this->values);
    }

    /**
     * Gets the index of a value
     *
     * @param mixed $value The value to search for
     * @return int|null The index of the value if it was found, otherwise null
     */
    public function indexOf($value) : ?int
    {
        if (($index = array_search($value, $this->values)) === false) {
            return null;
        }

        return (int)$index;
    }

    /**
     * Inserts the value at an index
     *
     * @param int $index The index to insert at
     * @param mixed $value The value to insert
     */
    public function insert(int $index, $value) : void
    {
        array_splice($this->values, $index, 0, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($index) : bool
    {
        return array_key_exists($index, $this->values);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($index)
    {
        return $this->get($index, null);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($index, $value) : void
    {
        $this->insert($index, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($index) : void
    {
        $this->removeIndex($index);
    }

    /**
     * Removes the value at an index
     *
     * @param int $index The index to remove
     */
    public function removeIndex(int $index) : void
    {
        unset($this->values[$index]);
    }

    /**
     * Removes the value from the list
     *
     * @param mixed $value The value to remove
     */
    public function removeValue($value) : void
    {
        $index = $this->indexOf($value);

        if ($index !== null) {
            $this->removeIndex($index);
        }
    }

    /**
     * Reverses the list
     */
    public function reverse() : void
    {
        $this->values = array_reverse($this->values);
    }

    /**
     * Sorts the values of the list
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
        return $this->values;
    }
}
