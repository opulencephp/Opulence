<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests Memcached
 */
namespace Opulence\Memcached;
use Opulence\Tests\Memcached\Mocks\OpulenceMemcached;
use Opulence\Tests\Memcached\Mocks\Server;

class OpulenceMemcachedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests adding multiple servers
     */
    public function testAddingMultipleServers()
    {
        $server1 = new Server();
        $server1->setPort(2);
        $server2 = new Server();
        $server2->setPort(3);
        $memcached = new OpulenceMemcached(new TypeMapper());
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
        $memcached = new OpulenceMemcached($typeMapper);
        $this->assertSame($typeMapper, $memcached->getTypeMapper());
    }
}