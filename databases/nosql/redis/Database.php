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
 * Defines a Redis database
 */
namespace RamODev\Databases\NoSQL\Redis;
use RamODev\Exceptions;
use RamODev\Databases;
use RamODev\Databases\NoSQL\Exceptions as CacheExceptions;

require_once(__DIR__ . "/../../Database.php");
require_once(__DIR__ . "/../exceptions/CacheException.php");

class Database extends Databases\Database
{
    /** @var Server The server we're connecting to */
    protected $server = null;
    /** @var \Redis The Redis object we use to cache items */
    private $redis = null;

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
     * Attempts to connect to the server
     *
     * @return bool True if we connected successfully, otherwise false
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
     * @return \Redis
     */
    public function getRedis()
    {
        return $this->redis;
    }

    /**
     * @return \RamODev\Databases\NoSQL\Redis\Server
     */
    public function getServer()
    {
        return $this->server;
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
}