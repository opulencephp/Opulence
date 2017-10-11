<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Redis\Tests;

use InvalidArgumentException;
use Predis\Client;
use Opulence\Redis\Redis;

/**
 * Tests the Redis wrapper
 */
class RedisTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests that commands go to the default client
     */
    public function testCommandsGoToDefaultClient()
    {
        $default = $this->getMockBuilder(Client::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $default->expects($this->any())
            ->method('get')
            ->with('baz')
            ->willReturn('foo');
        $foo = $this->getMockBuilder(Client::class)
            ->setMethods(['get'])
            ->disableOriginalConstructor()
            ->getMock();
        $foo->expects($this->any())
            ->method('get')
            ->willReturn('bar');
        $redis = new Redis(
            [
                'default' => $default,
                'foo' => $foo
            ]
        );
        $this->assertEquals('foo', $redis->get('baz'));
    }

    /**
     * Tests not passing a default
     */
    public function testNotPassingDefault()
    {
        $this->expectException(InvalidArgumentException::class);
        new Redis(['foo' => 'bar']);
    }

    /**
     * Tests passing an array of clients
     */
    public function testPassingArrayOfClients()
    {
        $default = $this->createMock(Client::class);
        $foo = $this->createMock(Client::class);
        $redis = new Redis(
            [
                'default' => $default,
                'foo' => $foo
            ]
        );
        $this->assertSame($default, $redis->getClient());
        $this->assertSame($default, $redis->getClient('default'));
        $this->assertSame($foo, $redis->getClient('foo'));
    }

    /**
     * Tests passing a single client
     */
    public function testPassingSingleClient()
    {
        $default = $this->createMock(Client::class);
        $redis = new Redis($default);
        $this->assertSame($default, $redis->getClient());
        $this->assertSame($default, $redis->getClient('default'));
    }
}
