<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Ioc\Tests\Bootstrappers\Dispatchers;

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
    private $dispatcher;
    /** @var Container The container to use in tests */
    private $container;

    public function setUp() : void
    {
        $this->container = new Container();
        $this->dispatcher = new BootstrapperDispatcher($this->container);
    }

    public function testDispatchingAllBootstrappersEagerly() : void
    {
        $this->dispatcher->dispatch([new EagerBootstrapper(), new LazyBootstrapper()]);
        $this->assertInstanceOf(LazyConcreteFoo::class, $this->container->resolve(LazyFooInterface::class));
        $this->assertInstanceOf(EagerConcreteFoo::class, $this->container->resolve(EagerFooInterface::class));
    }

    public function testDispatchingLazyDispatcherThatDependsOnDependencyFromLazyBootstrapperAndRegisteringItFirst() : void
    {
        ob_start();
        $this->dispatcher->dispatch([
            new LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper(),
            new LazyBootstrapper()
        ]);
        $this->container->resolve(EagerFooInterface::class);
        $this->assertEquals(LazyConcreteFoo::class, ob_get_clean());
    }

    public function testDispatchingLazyDispatcherThatDependsOnDependencyFromLazyBootstrapperAndRegisteringItSecond() : void
    {
        ob_start();
        $this->dispatcher->dispatch([
            new LazyBootstrapper(),
            new LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper()
        ]);
        $this->container->resolve(EagerFooInterface::class);
        $this->assertEquals(LazyConcreteFoo::class, ob_get_clean());
    }

    public function testLazyBootstrappersBindingsAreAvailableToContainer() : void
    {
        $this->dispatcher->dispatch([new LazyBootstrapper()]);
        $this->assertInstanceOf(LazyConcreteFoo::class, $this->container->resolve(LazyFooInterface::class));
    }

    public function testLazyTargetedBindingsAreAvailableInContainer() : void
    {
        $this->dispatcher->dispatch([new LazyBootstrapperWithTargetedBinding()]);
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

    public function testNotDispatchingAllBootstrappersEagerly() : void
    {
        $this->dispatcher->dispatch([new EagerBootstrapper(), new LazyBootstrapper()]);
        $this->assertTrue($this->container->hasBinding(LazyFooInterface::class));
        $this->assertInstanceOf(EagerConcreteFoo::class, $this->container->resolve(EagerFooInterface::class));
    }
}
