<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Memcached wrapper
 */
namespace Opulence\Memcached;

use InvalidArgumentException;

class Memcached
{
    /** @var array The list mapping of client names to instances */
    private $clients = [];
    /** @var TypeMapper The Memcached type mapper */
    private $typeMapper = null;

    /**
     * @param array|mixed $clients The client or list of clients
     * @param TypeMapper $typeMapper The Memcached type mapper
     * @throws InvalidArgumentException Thrown if no "default" client is specified when passing a list of clients
     */
    public function __construct($clients, TypeMapper $typeMapper)
    {
        if (is_array($clients)) {
            if (!isset($clients["default"])) {
                throw new InvalidArgumentException("Must specify a \"default\" client");
            }

            $this->clients = $clients;
        } else {
            $this->clients["default"] = $clients;
        }

        $this->typeMapper = $typeMapper;
    }

    /**
     * Calls the method on the default client
     *
     * @param string $name The name of the method to call
     * @param array $arguments The arguments to pass
     * @return mixed The return value of the method
     */
    public function __call($name, array $arguments)
    {
        return call_user_func_array([$this->getClient(), $name], $arguments);
    }

    /**
     * Gets the client with the input name
     *
     * @param string $name The name of the client to get
     * @return mixed The client
     * @throws InvalidArgumentException Thrown if no client with the input name exists
     */
    public function getClient($name = "default")
    {
        if (!isset($this->clients[$name])) {
            throw new InvalidArgumentException("No client with name \"$name\"");
        }

        return $this->clients[$name];
    }

    /**
     * Gets the type mapper used by this Memcached instance
     *
     * @return TypeMapper The type mapper used by this Memcached instance
     */
    public function getTypeMapper()
    {
        return $this->typeMapper;
    }
}