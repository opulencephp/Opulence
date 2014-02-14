<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Redis connection
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