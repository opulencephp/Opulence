<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Redis server
 */
namespace RamODev\Databases\NoSQL\Redis;

require_once(__DIR__ . "/../../../../databases/nosql/redis/Server.php");

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests setting the host
     */
    public function testSettingHost()
    {
        $ip = '127.0.0.1';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\NoSQL\\Redis\\Server");
        $server->setHost($ip);

        $this->assertEquals($ip, $server->getHost());
    }

    /**
     * Tests setting the lifetime
     */
    public function testSettingLifetime()
    {
        $lifetime = 12345;
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\NoSQL\\Redis\\Server");
        $server->setLifetime($lifetime);

        $this->assertEquals($lifetime, $server->getLifetime());
    }

    /**
     * Tests setting the display name
     */
    public function testSettingDisplayName()
    {
        $displayName = 'nicename';
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\NoSQL\\Redis\\Server");
        $server->setDisplayName($displayName);

        $this->assertEquals($displayName, $server->getDisplayName());
    }

    /**
     * Tests setting the port
     */
    public function testSettingPort()
    {
        $port = 11211;
        $server = $this->getMockForAbstractClass("RamODev\\Databases\\NoSQL\\Redis\\Server");
        $server->setPort($port);

        $this->assertEquals($port, $server->getPort());
    }
} 