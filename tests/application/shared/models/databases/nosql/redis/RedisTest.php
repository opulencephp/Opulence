<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Redis connection
 */
namespace RDev\Application\Shared\Models\Databases\NoSQL\Redis;
use RDev\Application\TBA\Models\Databases\NoSQL\Redis\Servers;

class RedisTest extends \PHPUnit_Framework_TestCase
{
    /** @var Redis The Redis object we're connecting to */
    private $redis = null;

    /**
     * Sets up the test
     */
    public function setUp()
    {
        $server = new Servers\ElastiCache();
        $this->redis = new Redis($server);
    }

    /**
     * Tests closing the connection
     */
    public function testClosingConnection()
    {
        $this->redis->connect();
        $this->redis->close();
        $this->assertFalse($this->redis->isConnected());
    }

    /**
     * Tests the connection
     */
    public function testConnection()
    {
        $this->assertTrue($this->redis->connect());
    }

    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $this->assertInstanceOf("RDev\\Application\\Shared\\Models\\Databases\\NoSQL\\Redis\\Server", $this->redis->getServer());
    }
}