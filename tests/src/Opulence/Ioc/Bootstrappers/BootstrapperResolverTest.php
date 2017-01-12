<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Bootstrappers;

use InvalidArgumentException;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\Bootstrapper;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\BootstrapperWithEverything;

/**
 * Tests the bootstrapper resolver
 */
class BootstrapperResolverTest extends \PHPUnit\Framework\TestCase
{
    /** @var BootstrapperResolver The resolver to use in tests */
    private $resolver = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->resolver = new BootstrapperResolver();
    }

    /**
     * Tests resolving a bootstrapper
     */
    public function testResolvingBootstrapper()
    {
        $bootstrapper = $this->resolver->resolve(Bootstrapper::class);
        $this->assertInstanceOf(Bootstrapper::class, $bootstrapper);
    }

    /**
     * Tests resolving a bootstrapper twice returns the same instance
     */
    public function testResolvingBootstrapperTwiceReturnsSameInstance()
    {
        $this->assertSame($this->resolver->resolve(Bootstrapper::class), $this->resolver->resolve(Bootstrapper::class));
    }

    /**
     * Tests resolving an invalid bootstrapper
     */
    public function testResolvingInvalidBootstrapper()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->resolver->resolve(self::class);
    }

    /**
     * Tests resolving many bootstrappers
     */
    public function testResolvingManyBootstrappers()
    {
        $bootstrappers = $this->resolver->resolveMany([Bootstrapper::class, BootstrapperWithEverything::class]);
        $this->assertInstanceOf(Bootstrapper::class, $bootstrappers[0]);
        $this->assertInstanceOf(BootstrapperWithEverything::class, $bootstrappers[1]);
    }

    /**
     * Tests resolving many bootstrappers twice returns the same instances
     */
    public function testResolvingManyBootstrappersTwiceReturnsSameInstances()
    {
        $bootstrappers1 = $this->resolver->resolveMany([Bootstrapper::class, BootstrapperWithEverything::class]);
        $bootstrappers2 = $this->resolver->resolveMany([Bootstrapper::class, BootstrapperWithEverything::class]);
        $this->assertSame($bootstrappers1[0], $bootstrappers2[0]);
        $this->assertSame($bootstrappers1[1], $bootstrappers2[1]);
    }
}
