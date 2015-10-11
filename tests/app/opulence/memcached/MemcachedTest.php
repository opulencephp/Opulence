<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the Memcached wrapper
 */
namespace Opulence\Memcached;

use InvalidArgumentException;
use Memcached as Client;

class MemcachedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Tests that commands go to the default client
     */
    public function testCommandsGoToDefaultClient()
    {
        $default = $this->getMock(Client::class, ["get"], [], "", false);
        $default->expects($this->any())
            ->method("get")
            ->with("baz")
            ->willReturn("foo");
        $foo = $this->getMock(Client::class, ["get"], [], "", false);
        $foo->expects($this->any())
            ->method("get")
            ->willReturn("bar");
        $memcached = new Memcached(
            [
                "default" => $default,
                "foo" => $foo
            ],
            new TypeMapper()
        );
        $this->assertEquals("foo", $memcached->get("baz"));
    }

    /**
     * Tests getting the type mapper
     */
    public function testGettingTypeMapper()
    {
        $typeMapper = new TypeMapper();
        $memcached = new Memcached($this->getMock(Client::class), $typeMapper);
        $this->assertSame($typeMapper, $memcached->getTypeMapper());
    }

    /**
     * Tests not passing a default
     */
    public function testNotPassingDefault()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new Memcached(["foo" => "bar"], new TypeMapper());
    }

    /**
     * Tests passing an array of clients
     */
    public function testPassingArrayOfClients()
    {
        $default = $this->getMock(Client::class);
        $foo = $this->getMock(Client::class);
        $memcached = new Memcached(
            [
                "default" => $default,
                "foo" => $foo
            ],
            new TypeMapper()
        );
        $this->assertSame($default, $memcached->getClient());
        $this->assertSame($default, $memcached->getClient("default"));
        $this->assertSame($foo, $memcached->getClient("foo"));
    }

    /**
     * Tests passing a single client
     */
    public function testPassingSingleClient()
    {
        $default = $this->getMock(Client::class);
        $memcached = new Memcached($default, new TypeMapper());
        $this->assertSame($default, $memcached->getClient());
        $this->assertSame($default, $memcached->getClient("default"));
    }
}