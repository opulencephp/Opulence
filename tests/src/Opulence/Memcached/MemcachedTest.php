<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Memcached;

use InvalidArgumentException;
use Memcached as Client;

/**
 * Tests the Memcached wrapper
 */
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
            ]
        );
        $this->assertEquals("foo", $memcached->get("baz"));
    }

    /**
     * Tests not passing a default
     */
    public function testNotPassingDefault()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new Memcached(["foo" => "bar"]);
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
            ]
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
        $memcached = new Memcached($default);
        $this->assertSame($default, $memcached->getClient());
        $this->assertSame($default, $memcached->getClient("default"));
    }
}