<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Memcached\Tests;

use InvalidArgumentException;
use Memcached as Client;
use Opulence\Memcached\Memcached;

/**
 * Tests the Memcached wrapper
 */
class MemcachedTest extends \PHPUnit\Framework\TestCase
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
        $memcached = new Memcached(
            [
                'default' => $default,
                'foo' => $foo
            ]
        );
        $this->assertEquals('foo', $memcached->get('baz'));
    }

    public function testNotPassingDefault(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Memcached(['foo' => 'bar']);
    }

    public function testPassingArrayOfClients(): void
    {
        $default = $this->createMock(Client::class);
        $foo = $this->createMock(Client::class);
        $memcached = new Memcached(
            [
                'default' => $default,
                'foo' => $foo
            ]
        );
        $this->assertSame($default, $memcached->getClient());
        $this->assertSame($default, $memcached->getClient('default'));
        $this->assertSame($foo, $memcached->getClient('foo'));
    }

    public function testPassingSingleClient(): void
    {
        $default = $this->createMock(Client::class);
        $memcached = new Memcached($default);
        $this->assertSame($default, $memcached->getClient());
        $this->assertSame($default, $memcached->getClient('default'));
    }
}
