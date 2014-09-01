<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the PHPRedis class
 */
namespace RDev\Models\Databases\NoSQL\Redis;
use RDev\Tests\Models\Databases\NoSQL\Redis\Mocks;

class RDevPHPRedisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $server = new Server();
        $config = [
            "servers" => [
                "master" => $server
            ]
        ];
        $redis = new Mocks\RDevPHPRedis($config);
        $this->assertEquals($server, $redis->getServer());
    }

    /**
     * Tests getting the type mapper
     */
    public function testGettingTypeMapper()
    {
        $config = [
            "servers" => [
                "master" => new Server()
            ]
        ];
        $redis = new Mocks\RDevPHPRedis($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\NoSQL\\Redis\\TypeMapper", $redis->getTypeMapper());
    }
} 