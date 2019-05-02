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
use Opulence\Ioc\Bootstrappers\Inspection\Caching\IInspectionBindingCache;
use Opulence\Ioc\Bootstrappers\Inspection\InspectionBinding;
use Opulence\Ioc\Bootstrappers\Inspection\InspectionBindingBootstrapperDispatcher;
use Opulence\Ioc\Bootstrappers\Inspection\UniversalInspectionBinding;
use Opulence\Ioc\IContainer;
use PHPUnit\Framework\TestCase;

/**
 * Tests the inspection binding bootstrapper dispatcher
 */
class InspectionBindingBootstrapperDispatcherTest extends TestCase
{
    /** @var InspectionBindingBootstrapperDispatcher */
    private $dispatcher;
    /** @var IContainer|MockObject */
    private $container;
    /** @var IInspectionBindingCache|MockObject */
    private $cache;

    protected function setUp(): void
    {
        $this->container = $this->createMock(IContainer::class);
        $this->cache = $this->createMock(IInspectionBindingCache::class);
        $this->dispatcher = new InspectionBindingBootstrapperDispatcher($this->container, $this->cache);
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
            ->with($this->callback(function (array $inspectionBindings) use ($expectedBootstrapper) {
                /** @var InspectionBinding[] $inspectionBindings */
                return \count($inspectionBindings) === 1
                    && $inspectionBindings[0]->getBootstrapper() === $expectedBootstrapper
                    && $inspectionBindings[0]->getInterface() === 'foo';
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
        $expectedBindings = [new UniversalInspectionBinding('foo', $expectedBootstrapper)];
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

    public function testDispatchingWithoutCacheForcesBindingInspection(): void
    {
        $dispatcher = new InspectionBindingBootstrapperDispatcher($this->container);
        $bootstrapper = new class extends Bootstrapper {
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
        $dispatcher->dispatch([$bootstrapper]);
    }
}
