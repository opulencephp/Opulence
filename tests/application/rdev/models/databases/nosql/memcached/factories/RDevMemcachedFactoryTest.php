<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests the Memcached factory
 */
namespace RDev\Models\Databases\NoSQL\Memcached\Factories;
use RDev\Models\Databases\NoSQL\Memcached\Configs;
use RDev\Tests\Models\Databases\NoSQL\Memcached\Factories\Mocks;
use RDev\Tests\Models\Databases\NoSQL\Memcached\Mocks as MemcachedMocks;

class RDevMemcachedFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\RDevMemcachedFactory The factory to use to create mock Memcache objects */
    private $factory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->factory = new Mocks\RDevMemcachedFactory();
    }

    /**
     * Tests passing in multiple servers
     */
    public function testPassingMultipleServers()
    {
        $server1 = new MemcachedMocks\Server();
        $server2 = new MemcachedMocks\Server();
        $configArray = [
            "servers" => [
                $server1,
                $server2
            ]
        ];
        $config = new Configs\ServerConfig($configArray);
        $memcached = $this->factory->createFromConfig($config);
        $this->assertEquals([$server1, $server2], $memcached->getServers());
    }

    /**
     * Tests passing in a single server
     */
    public function testPassingSingleServer()
    {
        $server = new MemcachedMocks\Server();
        $configArray = [
            "servers" => [
                $server
            ]
        ];
        $config = new Configs\ServerConfig($configArray);
        $memcached = $this->factory->createFromConfig($config);
        $this->assertEquals([$server], $memcached->getServers());
    }
}