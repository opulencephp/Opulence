<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Memcached;

use InvalidArgumentException;

/**
 * Defines the Memcached wrapper
 */
class Memcached
{
    /** @var array The list mapping of client names to instances */
    private $clients = [];

    /**
     * @param array|mixed $clients The client or list of clients
     * @throws InvalidArgumentException Thrown if no "default" client is specified when passing a list of clients
     */
    public function __construct($clients)
    {
        if (is_array($clients)) {
            if (!isset($clients['default'])) {
                throw new InvalidArgumentException("Must specify a \"default\" client");
            }

            $this->clients = $clients;
        } else {
            $this->clients['default'] = $clients;
        }
    }

    /**
     * Calls the method on the default client
     *
     * @param string $name The name of the method to call
     * @param array $arguments The arguments to pass
     * @return mixed The return value of the method
     */
    public function __call(string $name, array $arguments)
    {
        return $this->getClient()->$name(...$arguments);
    }

    /**
     * Gets the client with the input name
     *
     * @param string $name The name of the client to get
     * @return mixed The client
     * @throws InvalidArgumentException Thrown if no client with the input name exists
     */
    public function getClient(string $name = 'default')
    {
        if (!isset($this->clients[$name])) {
            throw new InvalidArgumentException("No client with name \"$name\"");
        }

        return $this->clients[$name];
    }
}
