<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the RDevRedis class
 */
namespace RDev\Models\Databases\NoSQL\Redis;
use TBA\Models\Databases\NoSQL\Redis\Servers;

class RDevRedisTest extends \PHPUnit_Framework_TestCase
{
    /** @var RDevRedis The RDevRedis object we're connecting to */
    private $redis = null;

    /**
     * Sets up the test
     */
    public function setUp()
    {
        $server = new Servers\ElastiCache();
        $this->redis = new RDevRedis($server);
    }

    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $this->assertInstanceOf("RDev\\Models\\Databases\\NoSQL\\Redis\\Server", $this->redis->getServer());
    }
}