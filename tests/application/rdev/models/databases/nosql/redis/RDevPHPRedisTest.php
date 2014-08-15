<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the PHPRedis class
 */
namespace RDev\Tests\Models\Databases\NoSQL\Redis;
use RDev\Tests\Models\Databases\NoSQL\Redis\Mocks;

class RDevPHPRedisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $server = new Mocks\Server();
        $redis = new Mocks\RDevPHPRedis($server);
        $this->assertEquals($server, $redis->getServer());
    }

    /**
     * Tests getting the type mapper
     */
    public function testGettingTypeMapper()
    {
        $server = new Mocks\Server();
        $redis = new Mocks\RDevPHPRedis($server);
        $this->assertInstanceOf("RDev\\Models\\Databases\\NoSQL\\Redis\\TypeMapper", $redis->getTypeMapper());
    }
} 