<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers\Dispatchers;

use Opulence\Ioc\Bootstrappers\BootstrapperRegistry;
use Opulence\Ioc\Bootstrappers\Dispatchers\BootstrapperDispatcher;
use Opulence\Ioc\Container;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\EagerBootstrapper;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\EagerConcreteFoo;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\EagerFooInterface;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\LazyBootstrapper;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\LazyBootstrapperWithTargetedBinding;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\LazyConcreteFoo;
use Opulence\Ioc\Tests\Bootstrappers\Mocks\LazyFooInterface;

/**
 * Tests the bootstrapper dispatcher
 */
class BootstrapperDispatcherTest extends \PHPUnit\Framework\TestCase
{
    /** @var BootstrapperDispatcher The dispatcher to use in tests */
    private $dispatcher = null;
    /** @var Container The container to use in tests */
    private $container = null;
    /** @var BootstrapperRegistry The bootstrapper registry */
    private $bootstrapperRegistry = null;

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->container = new Container();
        $this->bootstrapperRegistry = new BootstrapperRegistry();
        $this->dispatcher = new BootstrapperDispatcher(
            $this->container,
            $this->bootstrapperRegistry
        );
    }

    /**
     * Tests dispatching all bootstrappers eagerly
     */
    public function testDispatchingAllBootstrappersEagerly() : void
    {
        $this->bootstrapperRegistry->registerBootstrapper(new EagerBootstrapper());
        $this->bootstrapperRegistry->registerBootstrapper(new LazyBootstrapper());
        $this->dispatcher->dispatch(true);
        $this->assertInstanceOf(LazyConcreteFoo::class, $this->container->resolve(LazyFooInterface::class));
        $this->assertInstanceOf(EagerConcreteFoo::class, $this->container->resolve(EagerFooInterface::class));
    }

    /**
     * Tests dispatching an eager bootstrapper that depends on a dependency set in a lazy bootstrapper
     */
    public function testDispatchingEagerDispatcherThatDependsOnDependencyFromLazyBootstrapper() : void
    {
        $this->bootstrapperRegistry->registerBootstrapper(new EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper());
        $this->bootstrapperRegistry->registerBootstrapper(new LazyBootstrapper());
        ob_start();
        $this->dispatcher->dispatch(false);
        $this->assertEquals(LazyConcreteFoo::class, ob_get_clean());
    }

    /**
     * Tests dispatching a lazy bootstrapper (registered first) that depends on a dependency set in a lazy bootstrapper
     */
    public function testDispatchingLazyDispatcherThatDependsOnDependencyFromLazyBootstrapperAndRegisteringItFirst() : void
    {
        $this->bootstrapperRegistry->registerBootstrapper(
            new LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper()
        );
        $this->bootstrapperRegistry->registerBootstrapper(new LazyBootstrapper());
        ob_start();
        $this->dispatcher->dispatch(false);
        $this->container->resolve(EagerFooInterface::class);
        $this->assertEquals(LazyConcreteFoo::class, ob_get_clean());
    }

    /**
     * Tests dispatching a lazy bootstrapper (registered second) that depends on a dependency set in a lazy bootstrapper
     */
    public function testDispatchingLazyDispatcherThatDependsOnDependencyFromLazyBootstrapperAndRegisteringItSecond() : void
    {
        $this->bootstrapperRegistry->registerBootstrapper(new LazyBootstrapper());
        $this->bootstrapperRegistry->registerBootstrapper(
            new LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper()
        );
        ob_start();
        $this->dispatcher->dispatch(false);
        $this->container->resolve(EagerFooInterface::class);
        $this->assertEquals(LazyConcreteFoo::class, ob_get_clean());
    }

    /**
     * Tests that a lazy bootstrapper's bindings are available to the container
     */
    public function testLazyBootstrappersBindingsAreAvailableToContainer() : void
    {
        $this->bootstrapperRegistry->registerBootstrapper(new LazyBootstrapper());
        $this->dispatcher->dispatch(false);
        $this->assertInstanceOf(LazyConcreteFoo::class, $this->container->resolve(LazyFooInterface::class));
    }

    /**
     * Tests that lazy targeted bindings are available in the container
     */
    public function testLazyTargetedBindingsAreAvailableInContainer() : void
    {
        $this->bootstrapperRegistry->registerBootstrapper(
            new LazyBootstrapperWithTargetedBinding()
        );
        $this->dispatcher->dispatch(false);
        // Make sure it's bound to the target
        $this->assertTrue(
            $this->container->for(
                EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class,
                function (IContainer $container) {
                    return $container->hasBinding(LazyFooInterface::class);
                }
            )
        );
        $this->assertInstanceOf(
            LazyConcreteFoo::class,
            $this->container->for(
                EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class,
                function (IContainer $container) {
                    return $container->resolve(LazyFooInterface::class);
                }
            )
        );
        // Make sure it wasn't bound universally
        $notBoundUniversally = false;

        try {
            $this->container->resolve(LazyFooInterface::class);
            $this->fail('Targeted binding is accidentally universal');
        } catch (IocException $ex) {
            $notBoundUniversally = true;
        }

        $this->assertTrue($notBoundUniversally);
        $this->assertFalse($this->container->hasBinding(LazyFooInterface::class));
    }

    /**
     * Tests not dispatching all bootstrappers eagerly
     */
    public function testNotDispatchingAllBootstrappersEagerly() : void
    {
        $this->bootstrapperRegistry->registerBootstrapper(new EagerBootstrapper());
        $this->bootstrapperRegistry->registerBootstrapper(new LazyBootstrapper());
        $this->dispatcher->dispatch(false);
        $this->assertTrue($this->container->hasBinding(LazyFooInterface::class));
        $this->assertInstanceOf(EagerConcreteFoo::class, $this->container->resolve(EagerFooInterface::class));
    }
}
