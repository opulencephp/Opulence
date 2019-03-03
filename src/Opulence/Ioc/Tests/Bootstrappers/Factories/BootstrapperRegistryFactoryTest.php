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

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->factory = new BootstrapperRegistryFactory();
    }

    /**
     * Tests that creating a registry resolves the bootstrappers
     */
    public function testCreatingRegistryResolvesBootstrappers() : void
    {
        $bootstrapperClasses = [Bootstrapper::class, BootstrapperWithEverything::class];
        $bootstrapperRegistry = $this->factory->createBootstrapperRegistry($bootstrapperClasses);
        $this->assertEquals([Bootstrapper::class], $bootstrapperRegistry->getEagerBootstrappers());
        $this->assertEquals(
            [LazyFooInterface::class => ['bootstrapper' => BootstrapperWithEverything::class, 'target' => null]],
            $bootstrapperRegistry->getLazyBootstrapperBindings()
        );
    }
}
