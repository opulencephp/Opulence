<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Memcached server
 */
namespace RDev\Models\Databases\NoSQL\Memcached;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests setting the display name
     */
    public function testSettingDisplayName()
    {
        $displayName = 'nicename';
        $server = $this->getMockForAbstractClass("RDev\\Models\\Databases\\NoSQL\\Memcached\\Server");
        $server->setDisplayName($displayName);
        $this->assertEquals($displayName, $server->getDisplayName());
    }

    /**
     * Tests setting the host
     */
    public function testSettingHost()
    {
        $host = '127.0.0.1';
        $server = $this->getMockForAbstractClass("RDev\\Models\\Databases\\NoSQL\\Memcached\\Server");
        $server->setHost($host);
        $this->assertEquals($host, $server->getHost());
    }

    /**
     * Tests setting the port
     */
    public function testSettingPort()
    {
        $port = 11211;
        $server = $this->getMockForAbstractClass("RDev\\Models\\Databases\\NoSQL\\Memcached\\Server");
        $server->setPort($port);
        $this->assertEquals($port, $server->getPort());
    }

    /**
     * Tests setting the weight
     */
    public function testSettingWeight()
    {
        $weight = 63;
        $server = $this->getMockForAbstractClass("RDev\\Models\\Databases\\NoSQL\\Memcached\\Server");
        $server->setWeight($weight);
        $this->assertEquals($weight, $server->getWeight());
    }
} 