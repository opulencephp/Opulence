<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Redis server
 */
namespace RDev\Models\Databases\NoSQL\Redis;
use RDev\Tests\Models\Databases\NoSQL\Redis\Mocks;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests getting the port
     */
    public function testGettingPort()
    {
        $server = new Mocks\Server();
        $this->assertEquals(6379, $server->getPort());
    }
} 