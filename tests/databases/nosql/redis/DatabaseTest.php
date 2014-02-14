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
 * Tests our Redis connection
 */
namespace RamODev\Databases\NoSQL\Redis;
use RamODev\Databases\NoSQL\Redis\Servers;

require_once(__DIR__ . "/../../../../databases/nosql/redis/Database.php");
require_once(__DIR__ . "/../../../../databases/nosql/redis/servers/ElastiCache.php");

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Database The connection we're using */
    private $database = null;

    /**
     * Sets up our test
     */
    public function setUp()
    {
        $server = new Servers\ElastiCache();
        $this->database = new Database($server);
    }

    /**
     * Tests closing the connection
     */
    public function testClosingConnection()
    {
        $this->database->connect();
        $this->database->close();
        $this->assertFalse($this->database->isConnected());
    }

    /**
     * Tests the connection
     */
    public function testConnection()
    {
        $this->assertTrue($this->database->connect());
    }

    /**
     * Tests getting Redis
     */
    public function testGettingRedis()
    {
        $this->assertInstanceOf("\\Redis", $this->database->getRedis());
    }

    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $this->assertInstanceOf("\\RamODev\\Databases\\NoSQL\\Redis\\Server", $this->database->getServer());
    }
}