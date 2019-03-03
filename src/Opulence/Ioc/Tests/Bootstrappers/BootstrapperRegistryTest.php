<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers;

use Opulence\Ioc\Bootstrappers\BootstrapperRegistry;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\EagerBootstrapper;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\LazyBootstrapper;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\LazyFooInterface;

/**
 * Tests the bootstrapper registry
 */
class BootstrapperRegistryTest extends \PHPUnit\Framework\TestCase
{
    /** @var BootstrapperRegistry The registry to test */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp(): void
    {
        $this->registry = new BootstrapperRegistry();
    }

    /**
     * Tests registering and getting an eager bootstrapper
     */
    public function testRegisteringAndGettingEagerBootstrapper(): void
    {
        $expectedBootstrapper = new EagerBootstrapper();
        $this->registry->registerBootstrapper($expectedBootstrapper);
        $this->assertEquals([EagerBootstrapper::class], $this->registry->getEagerBootstrappers());
    }

    /**
     * Tests registering and getting a lazy bootstrapper with an array of bindings
     */
    public function testRegisteringAndGettingLazyBootstrapperWithSingleBinding(): void
    {
        $expectedBinding = new LazyBootstrapper();
        $this->registry->registerBootstrapper($expectedBinding);
        $this->assertEquals(
            [
                LazyFooInterface::class => ['bootstrapper' => LazyBootstrapper::class, 'target' => null]
            ],
            $this->registry->getLazyBootstrapperBindings()
        );
    }

    /**
     * Tests registering and getting a lazy bootstrapper with a single targeted binding
     */
    public function testRegisteringAndGettingLazyBootstrapperWithSingleTargetedBinding(): void
    {
        /** @var LazyBootstrapper|\PHPUnit_Framework_MockObject_MockObject $expectedBinding */
        $expectedBinding = $this->createMock(LazyBootstrapper::class);
        $expectedBinding->method('getBindings')
            ->willReturn([['foo' => 'bar']]);
        $this->registry->registerBootstrapper($expectedBinding);
        $this->assertEquals(
            [
                'foo' => ['bootstrapper' => get_class($expectedBinding), 'target' => 'bar']
            ],
            $this->registry->getLazyBootstrapperBindings()
        );
    }
}
