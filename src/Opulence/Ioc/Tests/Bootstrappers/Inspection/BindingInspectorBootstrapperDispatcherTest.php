<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Ioc\Tests\Bootstrappers\Inspection;

use Closure;
use Opulence\Collections\Tests\Mocks\MockObject;
use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\Bootstrappers\Inspection\BindingInspectorBootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\Inspection\BootstrapperBinding;
use Opulence\Ioc\Bootstrappers\Inspection\Caching\IBootstrapperBindingCache;
use Opulence\Ioc\Bootstrappers\Inspection\UniversalBootstrapperBinding;
use Opulence\Ioc\IContainer;
use PHPUnit\Framework\TestCase;

/**
 * Tests the inspection binding bootstrapper dispatcher
 */
class BindingInspectorBootstrapperDispatcherTest extends TestCase
{
    /** @var BindingInspectorBootstrapperDispatcher */
    private $dispatcher;
    /** @var IContainer|MockObject */
    private $container;
    /** @var IBootstrapperBindingCache|MockObject */
    private $cache;

    protected function setUp(): void
    {
        $this->container = $this->createMock(IContainer::class);
        $this->cache = $this->createMock(IBootstrapperBindingCache::class);
        $this->dispatcher = new BindingInspectorBootstrapperDispatcher($this->container, $this->cache);
    }

    public function testDispatchingWithCacheForcesBindingInspectionAndSetsCacheOnCacheMiss(): void
    {
        $expectedBootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->bindPrototype('foo', 'bar');
            }
        };
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturn(null);
        $this->cache->expects($this->once())
            ->method('set')
            ->with($this->callback(function (array $bootstrapperBindings) use ($expectedBootstrapper) {
                /** @var BootstrapperBinding[] $bootstrapperBindings */
                return \count($bootstrapperBindings) === 1
                    && $bootstrapperBindings[0]->getBootstrapper() === $expectedBootstrapper
                    && $bootstrapperBindings[0]->getInterface() === 'foo';
            }));
        $this->container->expects($this->once())
            ->method('bindFactory')
            ->with('foo', $this->callback(function (Closure $factory) {
                return true;
            }));
        $this->dispatcher->dispatch([$expectedBootstrapper]);
    }

    public function testDispatchingWithCacheUsesResultsOnCacheHit(): void
    {
        $expectedBootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->bindPrototype('foo', 'bar');
            }
        };
        $expectedBindings = [new UniversalBootstrapperBinding('foo', $expectedBootstrapper)];
        $this->cache->expects($this->once())
            ->method('get')
            ->willReturn($expectedBindings);
        $this->container->expects($this->once())
            ->method('bindFactory')
            ->with('foo', $this->callback(function (Closure $factory) {
                return true;
            }));
        $this->dispatcher->dispatch([$expectedBootstrapper]);
    }

    public function testDispatchingWithNoCacheForcesBindingInspection(): void
    {
        $dispatcher = new BindingInspectorBootstrapperDispatcher($this->container);
        $expectedBootstrapper = new class extends Bootstrapper {
            public function registerBindings(IContainer $container): void
            {
                $container->bindPrototype('foo', 'bar');
            }
        };
        $this->container->expects($this->once())
            ->method('bindFactory')
            ->with('foo', $this->callback(function (Closure $factory) {
                return true;
            }));
        $dispatcher->dispatch([$expectedBootstrapper]);
    }
}
