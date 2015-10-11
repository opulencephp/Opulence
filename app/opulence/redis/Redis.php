<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the Redis wrapper
 */
namespace Opulence\Redis;

use InvalidArgumentException;

class Redis
{
    /** @var array The list mapping of client names to instances */
    private $clients = [];
    /** @var TypeMapper The Redis type mapper */
    private $typeMapper = null;

    /**
     * @param array|mixed $clients The client or list of clients
     * @param TypeMapper $typeMapper The Redis type mapper
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
     * Deletes all the keys that match the input patterns
     * If you know the specific key(s) to delete, call Redis' delete command instead because this is relatively slow
     *
     * @param array|string The key pattern or list of key patterns to delete
     * @return bool True if successful, otherwise false
     */
    public function deleteKeyPatterns($keyPatterns)
    {
        if (is_string($keyPatterns)) {
            $keyPatterns = [$keyPatterns];
        }

        // Loops through our key patterns, gets all keys that match them, then deletes each of them
        $lua = "local keyPatterns = {'" . implode("','", $keyPatterns) . "'}
            for i, keyPattern in ipairs(keyPatterns) do
                for j, key in ipairs(redis.call('keys', keyPattern)) do
                    redis.call('del', key)
                end
            end";
        $this->eval($lua);

        return $this->getLastError() === null;
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
     * Gets the type mapper used by this Redis instance
     *
     * @return TypeMapper The type mapper used by this Redis instance
     */
    public function getTypeMapper()
    {
        return $this->typeMapper;
    }
}