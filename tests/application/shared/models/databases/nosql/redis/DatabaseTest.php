<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Redis connection
 */
namespace RamODev\Application\Shared\Models\Databases\NoSQL\Redis;
use RamODev\Application\TBA\Models\Databases\NoSQL\Redis\Servers;

class DatabaseTest extends \PHPUnit_Framework_TestCase
{
    /** @var Database The connection we're using */
    private $database = null;

    /**
     * Sets up the test
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
        $this->assertInstanceOf("\\Redis", $this->database->getPHPRedis());
    }

    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $this->assertInstanceOf("RamODev\\Application\\Shared\\Models\\Databases\\NoSQL\\Redis\\Server", $this->database->getServer());
    }
}