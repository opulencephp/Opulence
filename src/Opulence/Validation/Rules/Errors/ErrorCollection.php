<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Validation\Rules\Errors;

use ArrayAccess;
use Countable;

/**
 * Defines the error collection
 */
class ErrorCollection implements ArrayAccess, Countable
{
    /** @var array Defines the errors by field */
    protected $errorsByField = [];

    /**
     * @param array $values The list of values
     */
    public function __construct(array $values = [])
    {
        foreach ($values as $key => $value) {
            $this->add($key, $value);
        }
    }

    /**
     * Adds a value
     *
     * @param string $name The key to add
     * @param mixed $value The value to add
     */
    public function add(string $name, $value)
    {
        $this->errorsByField[$name] = $value;
    }

    /**
     * Gets the number of values
     *
     * @return int The number of values
     */
    public function count() : int
    {
        return count($this->errorsByField);
    }

    /**
     * Exchanges the current values with the input
     *
     * @param mixed $array The values to exchange with
     * @return array The old array
     */
    public function exchangeArray($array) : array
    {
        $oldValues = $this->errorsByField;
        $this->errorsByField = $array;

        return $oldValues;
    }

    /**
     * Gets the input value
     *
     * @param string $name The name of the value to get
     * @param mixed $default The default value
     * @return mixed The value of the value if it was found, otherwise the default value
     */
    public function get(string $name, $default = null)
    {
        return $this->has($name) ? $this->errorsByField[$name] : $default;
    }

    /**
     * Gets all of the values
     *
     * @return array All of the values
     */
    public function getAll() : array
    {
        return $this->errorsByField;
    }

    /**
     * Gets whether or not the key exists
     *
     * @param string $name The key to check for
     * @return bool True if the key exists, otherwise false
     */
    public function has(string $name) : bool
    {
        return isset($this->errorsByField[$name]);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset) : bool
    {
        return $this->has($offset);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->get($offset, null);
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        $this->remove($offset);
    }

    /**
     * Removes a key
     *
     * @param string $name The name of the key to remove
     */
    public function remove(string $name)
    {
        unset($this->errorsByField[$name]);
    }

    /**
     * Sets a value
     *
     * @param string $name The key to set
     * @param mixed $value The value to set
     */
    public function set(string $name, $value)
    {
        $this->errorsByField[$name] = $value;
    }
}
