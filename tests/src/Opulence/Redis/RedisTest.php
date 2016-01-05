<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Redis;

use InvalidArgumentException;
use Predis\Client;

/**
 * Tests the Redis wrapper
 */
class RedisTest extends \PHPUnit_Framework_TestCase
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
        $redis = new Redis(
            [
                "default" => $default,
                "foo" => $foo
            ]
        );
        $this->assertEquals("foo", $redis->get("baz"));
    }

    /**
     * Tests not passing a default
     */
    public function testNotPassingDefault()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new Redis(["foo" => "bar"]);
    }

    /**
     * Tests passing an array of clients
     */
    public function testPassingArrayOfClients()
    {
        $default = $this->getMock(Client::class);
        $foo = $this->getMock(Client::class);
        $redis = new Redis(
            [
                "default" => $default,
                "foo" => $foo
            ]
        );
        $this->assertSame($default, $redis->getClient());
        $this->assertSame($default, $redis->getClient("default"));
        $this->assertSame($foo, $redis->getClient("foo"));
    }

    /**
     * Tests passing a single client
     */
    public function testPassingSingleClient()
    {
        $default = $this->getMock(Client::class);
        $redis = new Redis($default);
        $this->assertSame($default, $redis->getClient());
        $this->assertSame($default, $redis->getClient("default"));
    }
}