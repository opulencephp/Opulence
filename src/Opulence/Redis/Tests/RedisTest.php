<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Redis\Tests;

use InvalidArgumentException;
use Opulence\Redis\Redis;
use Predis\Client;

/**
 * Tests the Redis wrapper
 */
class RedisTest extends \PHPUnit\Framework\TestCase
{
    public function testCommandsGoToDefaultClient(): void
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

    public function testNotPassingDefault(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Redis(['foo' => 'bar']);
    }

    public function testPassingArrayOfClients(): void
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

    public function testPassingSingleClient(): void
    {
        $default = $this->createMock(Client::class);
        $redis = new Redis($default);
        $this->assertSame($default, $redis->getClient());
        $this->assertSame($default, $redis->getClient('default'));
    }
}
