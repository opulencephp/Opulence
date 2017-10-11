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
use Countable;
use IteratorAggregate;
use Traversable;

/**
 * Defines a read-only hash table
 */
class ReadOnlyHashTable implements Countable, IteratorAggregate
{
    /** @var array The list of values */
    protected $values = [];

    /**
     * @param array $values The list of values
     */
    public function __construct(array $values)
    {
        foreach ($values as $key => $value) {
            $this->values[$key] = $value;
        }
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
     * Gets all of the values as an array
     *
     * @return array All of the values
     */
    public function toArray() : array
    {
        return $this->values;
    }
}
