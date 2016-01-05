<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Http;

use ArrayAccess;
use Countable;

/**
 * Defines the base class for HTTP connection parameter collections
 */
class Collection implements ArrayAccess, Countable
{
    /** @var array The list of values */
    protected $values = [];

    /**
     * @param array $values The list of values
     */
    public function __construct(array $values)
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
    public function add($name, $value)
    {
        $this->values[$name] = $value;
    }

    /**
     * Gets the number of values
     *
     * @return int The number of values
     */
    public function count()
    {
        return count($this->values);
    }

    /**
     * Exchanges the current values with the input
     *
     * @param mixed $array The values to exchange with
     * @return array The old array
     */
    public function exchangeArray($array)
    {
        $oldValues = $this->values;
        $this->values = $array;

        return $oldValues;
    }

    /**
     * Gets the input value
     *
     * @param string $name The name of the value to get
     * @param mixed $default The default value
     * @return mixed The value of the value if it was found, otherwise the default value
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->values[$name] : $default;
    }

    /**
     * Gets all of the values
     *
     * @return array All of the values
     */
    public function getAll()
    {
        return $this->values;
    }

    /**
     * Gets whether or not the key exists
     *
     * @param string $name The key to check for
     * @return bool True if the key exists, otherwise false
     */
    public function has($name)
    {
        return isset($this->values[$name]);
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
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
    public function remove($name)
    {
        unset($this->values[$name]);
    }

    /**
     * Sets a value
     *
     * @param string $name The key to set
     * @param mixed $value The value to set
     */
    public function set($name, $value)
    {
        $this->values[$name] = $value;
    }
} 