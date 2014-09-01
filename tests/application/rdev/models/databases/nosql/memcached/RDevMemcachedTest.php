<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests Memcached
 */
namespace RDev\Models\Databases\NoSQL\Memcached;
use RDev\Tests\Models\Databases\NoSQL\Memcached\Mocks;

class RDevMemcachedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding multiple servers
     */
    public function testAddingMultipleServers()
    {
        $server1 = new Mocks\Server();
        $server1->setPort(1);
        $server2 = new Mocks\Server();
        $server2->setPort(2);
        $server3 = new Mocks\Server();
        $server3->setPort(3);
        $config = [
            "servers" => [
                $server1
            ]
        ];
        $memcached = new Mocks\RDevMemcached($config);
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
        $config = [
            "servers" => [
                new Mocks\Server()
            ]
        ];
        $redis = new Mocks\RDevMemcached($config);
        $this->assertInstanceOf("RDev\\Models\\Databases\\NoSQL\\Memcached\\TypeMapper", $redis->getTypeMapper());
    }

    /**
     * Tests passing an invalid config
     */
    public function testPassingInvalidConfig()
    {
        $this->setExpectedException("\\RuntimeException");
        new Mocks\RDevMemcached(["Bad config"]);
    }

    /**
     * Tests passing in multiple servers
     */
    public function testPassingMultipleServers()
    {
        $server1 = new Mocks\Server();
        $server2 = new Mocks\Server();
        $config = [
            "servers" => [
                $server1,
                $server2
            ]
        ];
        $memcached = new Mocks\RDevMemcached($config);
        $this->assertEquals([$server1, $server2], $memcached->getServers());
    }

    /**
     * Tests passing in a single server
     */
    public function testPassingSingleServer()
    {
        $server = new Mocks\Server();
        $config = [
            "servers" => [
                $server
            ]
        ];
        $memcached = new Mocks\RDevMemcached($config);
        $this->assertEquals([$server], $memcached->getServers());
    }
}