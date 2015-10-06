<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the base class for HTTP connection parameters
 */
namespace Opulence\HTTP;

use ArrayAccess;
use Countable;

class Parameters implements ArrayAccess, Countable
{
    /** @var array The list of parameters */
    protected $parameters = [];

    /**
     * @param array $parameters The list of parameters
     */
    public function __construct(array $parameters)
    {
        foreach ($parameters as $key => $value) {
            $this->add($key, $value);
        }
    }

    /**
     * Adds a parameter
     *
     * @param string $name The name of the parameter to add
     * @param mixed $value The value of the parameter
     */
    public function add($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * Gets the number of parameters
     *
     * @return int The number of parameters
     */
    public function count()
    {
        return count($this->parameters);
    }

    /**
     * Exchanges the current parameters with the input
     *
     * @param mixed $array The parameters to exchange with
     * @return array The old array
     */
    public function exchangeArray($array)
    {
        $oldParameters = $this->parameters;
        $this->parameters = $array;

        return $oldParameters;
    }

    /**
     * Gets the input parameter
     *
     * @param string $name The name of the parameter to get
     * @param mixed $default The default value
     * @return mixed The value of the parameter if it was found, otherwise the default value
     */
    public function get($name, $default = null)
    {
        return $this->has($name) ? $this->parameters[$name] : $default;
    }

    /**
     * Gets all of the parameters
     *
     * @return array All of the parameters
     */
    public function getAll()
    {
        return $this->parameters;
    }

    /**
     * Gets whether or not the parameter exists
     *
     * @param string $name The name of the parameter to check for
     * @return bool True if the parameter exists, otherwise false
     */
    public function has($name)
    {
        return isset($this->parameters[$name]);
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
     * Removes a parameter
     *
     * @param string $name The name of the parameter to remove
     */
    public function remove($name)
    {
        unset($this->parameters[$name]);
    }

    /**
     * Sets a parameter
     *
     * @param string $name The name of the parameter to set
     * @param mixed $value The value of the parameter
     */
    public function set($name, $value)
    {
        $this->parameters[$name] = $value;
    }
} 