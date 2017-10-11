<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers\Factories;

use Opulence\Ioc\Bootstrappers\Factories\BootstrapperRegistryFactory;
use Opulence\Ioc\Bootstrappers\IBootstrapperResolver;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\Bootstrapper;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\BootstrapperWithEverything;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\LazyFooInterface;

/**
 * Tests the bootstrapper registry factory
 */
class BootstrapperRegistryFactoryTest extends \PHPUnit\Framework\TestCase
{
    /** @var BootstrapperRegistryFactory The factory to use in tests */
    private $factory = null;
    /** @var IBootstrapperResolver|\PHPUnit_Framework_MockObject_MockObject The bootstrapper resolver */
    private $resolver = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->resolver = $this->createMock(IBootstrapperResolver::class);
        $this->factory = new BootstrapperRegistryFactory($this->resolver);
    }

    /**
     * Tests that creating a registry resolves the bootstrappers
     */
    public function testCreatingRegistryResolvesBootstrappers()
    {
        $bootstrapperClasses = [Bootstrapper::class, BootstrapperWithEverything::class];
        $bootstrapperObjects = [new Bootstrapper(), new BootstrapperWithEverything()];
        $this->resolver->expects($this->any())
            ->method('resolveMany')
            ->with($bootstrapperClasses)
            ->willReturn($bootstrapperObjects);
        $bootstrapperRegistry = $this->factory->createBootstrapperRegistry($bootstrapperClasses);
        $this->assertEquals([Bootstrapper::class], $bootstrapperRegistry->getEagerBootstrappers());
        $this->assertEquals(
            [LazyFooInterface::class => ['bootstrapper' => BootstrapperWithEverything::class, 'target' => null]],
            $bootstrapperRegistry->getLazyBootstrapperBindings()
        );
    }
}
