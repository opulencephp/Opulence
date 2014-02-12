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
 * Tests our cache
 */
namespace RamODev\Storage\Cache\Redis;
use RamODev\Storage\Cache\Redis\Servers;

require_once(__DIR__ . "/../../../../storage/cache/redis/Connection.php");
require_once(__DIR__ . "/../../../../storage/cache/redis/servers/ElastiCache.php");

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Server A database server to connect to */
    private $server = null;
    /** @var Connection The connection we're using */
    private $connection = null;

    /**
     * Sets up our test
     */
    public function setUp()
    {
        $this->server = new Servers\ElastiCache();
        $this->connection = new Connection($this->server);
    }

    /**
     * Tests committing a transaction
     */
    public function testCommittingTransaction()
    {
        $value = "test";
        $this->connection->connect();
        $this->connection->delete(get_called_class());
        $this->connection->startTransaction();
        $this->connection->write($value, get_called_class());
        $this->connection->commitTransaction();
        $this->assertEquals($value, $this->connection->read(get_called_class()));
    }

    /**
     * Tests flushing the cache
     */
    public function testFlushingCache()
    {
        $key1 = "test1";
        $key2 = "test2";
        $this->connection->connect();
        $this->connection->write("blah1", $key1);
        $this->connection->write("blah2", $key2);
        $this->connection->flush();
        $this->assertFalse($this->connection->read($key1));
        $this->assertFalse($this->connection->read($key2));
    }

    /**
     * Tests checking that we're connected after making a connection
     */
    public function testIsConnected()
    {
        $this->assertTrue($this->connection->connect() && $this->connection->isConnected());
    }

    /**
     * Tests reading a key in cache that doesn't exist
     */
    public function testReadingKeyThatDoesntExist()
    {
        $key = get_called_class();
        $this->connection->connect();
        $this->connection->delete($key);
        $this->assertFalse($this->connection->read($key));
    }

    /**
     * Tests rolling back a transaction
     */
    public function testRollingBackTransaction()
    {
        $this->connection->connect();
        $this->connection->delete(get_called_class());
        $this->connection->startTransaction();
        $this->connection->write("test", get_called_class());
        $this->connection->rollBackTransaction();
        $this->assertFalse($this->connection->read(get_called_class()));
    }

    /**
     * Tests starting a transaction when one has already been started
     */
    public function testStartingTransactionWhenThereIsAlreadyATransaction()
    {
        $this->setExpectedException("RamODev\\Storage\\Cache\\Exceptions\\CacheException");
        $this->connection->connect();
        $this->connection->startTransaction();
        $this->connection->startTransaction();
    }

    /**
     * Tests reading an object to cache
     */
    public function testWritingObject()
    {
        $object = new \stdClass();
        $object->data = "blah";
        $this->connection->connect();
        $this->connection->write($object, get_called_class());
        $this->assertEquals($object, $this->connection->read(get_called_class()));
    }

    /**
     * Tests writing a string to cache
     */
    public function testWritingString()
    {
        $value = "test";
        $this->connection->connect();
        $this->connection->write($value, get_called_class());
        $this->assertEquals($value, $this->connection->read(get_called_class()));
    }

    /**
     * Tests writing to a cache that we're not connected to
     */
    public function testWritingToDisconnectedCache()
    {
        $value = "test";
        $this->connection->write($value, get_called_class());
        $this->assertFalse($this->connection->read(get_called_class()));
    }
}