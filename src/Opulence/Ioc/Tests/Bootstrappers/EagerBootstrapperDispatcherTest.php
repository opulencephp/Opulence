<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests\Bootstrappers;

use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\EagerBootstrapperDispatcher;
use Opulence\Ioc\IContainer;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Tests the eager bootstrapper
 */
class EagerBootstrapperDispatcherTest extends TestCase
{
    /** @var EagerBootstrapperDispatcher */
    private $dispatcher;
    /** @var IContainer|MockObject */
    private $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(IContainer::class);
        $this->dispatcher = new EagerBootstrapperDispatcher($this->container);
    }

    public function testDispatchJustRegistersAllBindingsImmediately(): void
    {
        $expectedBootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->bindPrototype('foo', 'bar');
            }
        };
        $this->container->expects($this->once())
            ->method('bindPrototype')
            ->with('foo', 'bar');
        $this->dispatcher->dispatch([$expectedBootstrapper]);
    }
}
