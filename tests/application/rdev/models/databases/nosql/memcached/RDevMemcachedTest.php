<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Tests Memcached
 */
namespace RDev\Models\Databases\NoSQL\Memcached;
use RDev\Tests\Models\Databases\NoSQL\Memcached\Mocks as MemcachedMocks;

class RDevMemcachedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding multiple servers
     */
    public function testAddingMultipleServers()
    {
        $server1 = new MemcachedMocks\Server();
        $server1->setPort(2);
        $server2 = new MemcachedMocks\Server();
        $server2->setPort(3);
        $memcached = new MemcachedMocks\RDevMemcached(new TypeMapper());
        $memcached->addServers([
            [
                $server1->getHost(),
                $server1->getPort(),
                $server1->getWeight()
            ],
            [
                $server2->getHost(),
                $server2->getPort(),
                $server2->getWeight()
            ]
        ]);
        $this->assertEquals([$server1, $server2], $memcached->getServers());
    }

    /**
     * Tests getting the type mapper
     */
    public function testGettingTypeMapper()
    {
        $typeMapper = new TypeMapper();
        $memcached = new MemcachedMocks\RDevMemcached($typeMapper);
        $this->assertSame($typeMapper, $memcached->getTypeMapper());
    }
}