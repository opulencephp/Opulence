<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests Memcached
 */
namespace RDev\Models\Databases\NoSQL\Memcached;
use RDev\Tests\Models\Databases\NoSQL\Memcached\Mocks as MemcachedMocks;
use RDev\Tests\Models\Databases\NoSQL\Memcached\Factories\Mocks;

class RDevMemcachedTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mocks\RDevMemcachedFactory The factory to use to create mock Memcache objects */
    private $rDevMemcachedFactory = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->rDevMemcachedFactory = new Mocks\RDevMemcachedFactory();
    }

    /**
     * Tests adding multiple servers
     */
    public function testAddingMultipleServers()
    {
        $server1 = new MemcachedMocks\Server();
        $server1->setPort(1);
        $server2 = new MemcachedMocks\Server();
        $server2->setPort(2);
        $server3 = new MemcachedMocks\Server();
        $server3->setPort(3);
        $configArray = [
            "servers" => [
                $server1
            ]
        ];
        $config = new Configs\ServerConfig($configArray);
        $memcached = $this->rDevMemcachedFactory->createFromConfig($config);
        $memcached->addServers([
            [
                $server2->getHost(),
                $server2->getPort(),
                $server2->getWeight()
            ],
            [
                $server3->getHost(),
                $server3->getPort(),
                $server3->getWeight()
            ]
        ]);
        $this->assertEquals([$server1, $server2, $server3], $memcached->getServers());
    }

    /**
     * Tests getting the type mapper
     */
    public function testGettingTypeMapper()
    {
        $configArray = [
            "servers" => [
                new MemcachedMocks\Server()
            ]
        ];
        $config = new Configs\ServerConfig($configArray);
        $memcached = $this->rDevMemcachedFactory->createFromConfig($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\NoSQL\\Memcached\\TypeMapper", $memcached->getTypeMapper());
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
        $memcached = $this->rDevMemcachedFactory->createFromConfig($config);
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
        $memcached = $this->rDevMemcachedFactory->createFromConfig($config);
        $this->assertEquals([$server], $memcached->getServers());
    }
}