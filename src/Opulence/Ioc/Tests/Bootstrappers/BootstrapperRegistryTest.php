<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers;

use InvalidArgumentException;
use Opulence\Ioc\Bootstrappers\BootstrapperRegistry;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\EagerBootstrapper;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\EagerFooInterface;
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
    public function setUp()
    {
        $this->registry = new BootstrapperRegistry();
    }

    /**
     * Tests an invalid lazy bootstrapper binding
     */
    public function testInvalidLazyBootstrapperBinding()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->registry->registerLazyBootstrapper([[]], LazyBootstrapper::class);
    }

    /**
     * Tests registering and getting an eager bootstrapper
     */
    public function testRegisteringAndGettingEagerBootstrapper()
    {
        $this->registry->registerEagerBootstrapper(EagerBootstrapper::class);
        $this->assertEquals([EagerBootstrapper::class], $this->registry->getEagerBootstrappers());
    }

    /**
     * Tests registering and getting a lazy bootstrapper with an array of bindings
     */
    public function testRegisteringAndGettingLazyBootstrapperWithManyBindings()
    {
        $bindings = [LazyFooInterface::class, EagerFooInterface::class];
        $this->registry->registerLazyBootstrapper($bindings, LazyBootstrapper::class);
        $this->assertEquals(
            [
                LazyFooInterface::class => ['bootstrapper' => LazyBootstrapper::class, 'target' => null],
                EagerFooInterface::class => ['bootstrapper' => LazyBootstrapper::class, 'target' => null]
            ],
            $this->registry->getLazyBootstrapperBindings()
        );
    }

    /**
     * Tests registering and getting a lazy bootstrapper with a single binding
     */
    public function testRegisteringAndGettingLazyBootstrapperWithSingleBinding()
    {
        $this->registry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
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
    public function testRegisteringAndGettingLazyBootstrapperWithSingleTargetedBinding()
    {
        $this->registry->registerLazyBootstrapper([['foo' => 'bar']], 'baz');
        $this->assertEquals(
            [
                'foo' => ['bootstrapper' => 'baz', 'target' => 'bar']
            ],
            $this->registry->getLazyBootstrapperBindings()
        );
    }
}
