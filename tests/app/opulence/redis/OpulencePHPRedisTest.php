<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the PHPRedis class
 */
namespace Opulence\Redis;

use Opulence\Tests\Redis\Mocks\OpulencePHPRedis;

class OpulencePHPRedisTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the server
     */
    public function testGettingServer()
    {
        $server = new Server();
        $redis = new OpulencePHPRedis($server, new TypeMapper());
        $this->assertSame($server, $redis->getServer());
    }

    /**
     * Tests getting the type mapper
     */
    public function testGettingTypeMapper()
    {
        $server = new Server();
        $typeMapper = new TypeMapper();
        $redis = new OpulencePHPRedis($server, $typeMapper);
        $this->assertSame($typeMapper, $redis->getTypeMapper());
    }

    /**
     * Tests selecting the database
     */
    public function testSelectingDatabase()
    {
        $server = new Server();
        $typeMapper = new TypeMapper();
        $redis = new OpulencePHPRedis($server, $typeMapper);
        $redis->select(724);
        $this->assertEquals(724, $redis->getServer()->getDatabaseIndex());
    }
} 