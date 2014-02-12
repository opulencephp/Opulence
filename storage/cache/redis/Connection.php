<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of
 * this software and associated documentation files (the "Software"), to deal in
 * the Software without restriction, including without limitation the rights to
 * use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies
 * of the Software, and to permit persons to whom the Software is furnished to do
 * so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 *
 *
 * Connects to a Redis server
 */
namespace Storage\Cache\Redis;
use Exceptions;
use Storage;
use Storage\Cache\Exceptions as CacheExceptions;

require_once(__DIR__ . "/../../Connection.php");
require_once(__DIR__ . "/../exceptions/CacheException.php");

class Connection extends Storage\Connection
{
    /** @var Server The server we're connecting to */
    protected $server = null;
    /** @var \Redis The Redis object we use to cache items */
    private $redis = null;
    /** @var bool Whether or not we're in the middle of a transaction */
    private $inTransaction = false;

    /**
     * @param Server $server The server we're connecting to
     */
    public function __construct(Server $server)
    {
        parent::__construct($server);

        $this->redis = new \Redis();
    }

    /**
     * Closes the connection
     */
    public function close()
    {
        if($this->isConnected())
        {
            $this->redis->close();
        }
    }

    /**
     * Executes the transaction
     *
     * @returns bool True if the transaction executed successfully, otherwise false
     */
    public function commitTransaction()
    {
        if($this->isConnected())
        {
            $wasSuccessful = $this->redis->exec();
            $this->inTransaction = false;

            return $wasSuccessful;
        }

        return false;
    }

    /**
     * Attempts to connect to the server
     *
     * @returns bool True if we connected successfully, otherwise false
     */
    public function connect()
    {
        $this->redis->connect($this->server->getHost(), $this->server->getPort());

        if(!$this->isConnected())
        {
            Exceptions\Log::write("Unable to connect to cache on host " . $this->server->getHost());
        }

        return $this->isConnected();
    }

    /**
     * Deletes the item in cache with the specified key
     *
     * @param string $key The key to use when deleting the value from cache
     */
    public function delete($key)
    {
        if($this->isConnected())
        {
            $this->redis->delete($key);
        }
    }

    /**
     * Flushes the cache
     */
    public function flush()
    {
        if($this->isConnected())
        {
            $this->redis->flushAll();
        }
    }

    /**
     * Gets whether or not we're connected
     *
     * @return bool True if we're connected, otherwise false
     */
    public function isConnected()
    {
        return $this->redis->isConnected();
    }

    /**
     * Reads a value from cache
     *
     * @param string $key The key we're searching for
     * @return bool|mixed|Connection An unserialized object if we find the input key, otherwise $this if we're in a transaction, otherwise false if couldn't find the key
     */
    public function read($key)
    {
        if($this->isConnected())
        {
            if($this->inTransaction)
            {
                $this->redis->get($key);

                return $this;
            }
            else
            {
                return unserialize($this->redis->get($key));
            }
        }

        return false;
    }

    /**
     * Rolls back the current transaction
     */
    public function rollBackTransaction()
    {
        if($this->isConnected())
        {
            $this->redis->discard();
            $this->inTransaction = false;
        }
    }

    /**
     * Starts an transaction
     *
     * @return null|\Redis The Redis instance if we're connected, otherwise null
     * @throws CacheExceptions\CacheException Thrown if we're already in a transaction
     */
    public function startTransaction()
    {
        if($this->isConnected())
        {
            if($this->inTransaction)
            {
                throw new CacheExceptions\CacheException("Already in a transaction");
            }

            $this->inTransaction = true;

            return $this->redis->multi();
        }

        return null;
    }

    /**
     * Stores the input value under the input key
     *
     * @param mixed $value The unserialized value we're caching
     * @param string $key The key we're using to store under
     * @param int $lifetime The integer representing the lifetime (in seconds) for this item in cache
     * @return bool|Connection True if the operation was successful, otherwise $this if we're in a transaction, otherwise false
     */
    public function write($value, $key, $lifetime = -1)
    {
        if($this->isConnected())
        {
            // If the lifetime wasn't specified, use the server's default lifetime
            if($lifetime == -1)
            {
                $lifetime = $this->server->getLifetime();
            }

            if($this->inTransaction)
            {
                $this->redis->set($key, serialize($value));
                $this->redis->setTimeout($key, $lifetime);

                return $this;
            }
            else
            {
                return $this->redis->set($key, serialize($value)) && $this->redis->setTimeout($key, $lifetime);
            }
        }

        return true;
    }
}