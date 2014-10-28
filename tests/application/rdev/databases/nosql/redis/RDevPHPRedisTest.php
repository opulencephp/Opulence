<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the PHPRedis class
 */
namespace RDev\Databases\NoSQL\Redis;
use RDev\Tests\Databases\NoSQL\Redis\Mocks;

class RDevPHPRedisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $server = new Server();
        $redis = new Mocks\RDevPHPRedis($server, new TypeMapper());
        $this->assertSame($server, $redis->getServer());
    }

    /**
     * Tests getting the type mapper
     */
    public function testGettingTypeMapper()
    {
        $server = new Server();
        $typeMapper = new TypeMapper();
        $redis = new Mocks\RDevPHPRedis($server, $typeMapper);
        $this->assertSame($typeMapper, $redis->getTypeMapper());
    }
} 