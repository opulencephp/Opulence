<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers\Factories;

use Opulence\Ioc\Bootstrappers\BootstrapperRegistry;
use Opulence\Ioc\Bootstrappers\Caching\ICache;
use Opulence\Ioc\Bootstrappers\Factories\CachedBootstrapperRegistryFactory;
use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\Bootstrapper;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\BootstrapperWithEverything;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\LazyFooInterface;

/**
 * Tests the cached bootstrapper registry factory
 */
class CachedBootstrapperRegistryFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var CachedBootstrapperRegistryFactory The factory to use in tests */
    private $factory = null;
    /** @var ICache|\PHPUnit_Framework_MockObject_MockObject The bootstrapper registry cache */
    private $cache = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->cache = $this->createMock(ICache::class);
        $this->factory = new CachedBootstrapperRegistryFactory($this->cache);
    }

    /**
     * Tests that a cache hit returns that registry
     */
    public function testCacheHitReturnsThatRegistry() : void
    {
        $bootstrapperRegistry = new BootstrapperRegistry();
        $this->cache->expects($this->any())
            ->method('get')
            ->willReturn($bootstrapperRegistry);
        $this->assertSame($bootstrapperRegistry, $this->factory->createBootstrapperRegistry([]));
    }

    /**
     * Tests that a cache miss manually creates the registry
     */
    public function testCacheMissManuallyCreatesRegistry() : void
    {
        $bootstrapperClasses = [Bootstrapper::class, BootstrapperWithEverything::class];
        $lazyBootstrapperBindings = [
            LazyFooInterface::class => [
                'bootstrapper' => BootstrapperWithEverything::class,
                'target' => null
            ]
        ];
        $this->cache->expects($this->any())
            ->method('get')
            ->willReturn(null);
        $this->cache->expects($this->any())
            ->method('set')
            ->with($this->callback(function (IBootstrapperRegistry $bootstrapperRegistry) use ($lazyBootstrapperBindings
            ) {
                return $bootstrapperRegistry->getEagerBootstrappers() === [Bootstrapper::class]
                    && $bootstrapperRegistry->getLazyBootstrapperBindings() === $lazyBootstrapperBindings;
            }));
        $bootstrapperRegistry = $this->factory->createBootstrapperRegistry($bootstrapperClasses);
        $this->assertEquals([Bootstrapper::class], $bootstrapperRegistry->getEagerBootstrappers());
        $this->assertEquals($lazyBootstrapperBindings, $bootstrapperRegistry->getLazyBootstrapperBindings());
    }
}
