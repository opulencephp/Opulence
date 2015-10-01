<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the bootstrapper dispatcher
 */
namespace Opulence\Applications\Bootstrappers\Dispatchers;

use Closure;
use Opulence\IoC\Container;
use Opulence\Applications\Application;
use Opulence\Applications\Bootstrappers\BootstrapperRegistry;
use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Paths;
use Opulence\Applications\Tasks\Dispatchers\Dispatcher as TaskDispatcher;
use Opulence\Tests\Applications\Bootstrappers\Mocks\EagerBootstrapper;
use Opulence\Tests\Applications\Bootstrappers\Mocks\EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper;
use Opulence\Tests\Applications\Bootstrappers\Mocks\EagerFooInterface;
use Opulence\Tests\Applications\Bootstrappers\Mocks\EagerConcreteFoo;
use Opulence\Tests\Applications\Bootstrappers\Mocks\EnvironmentBootstrapper;
use Opulence\Tests\Applications\Bootstrappers\Mocks\LazyBootstrapper;
use Opulence\Tests\Applications\Bootstrappers\Mocks\LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper;
use Opulence\Tests\Applications\Bootstrappers\Mocks\LazyConcreteFoo;
use Opulence\Tests\Applications\Bootstrappers\Mocks\LazyFooInterface;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Dispatcher The dispatcher to use in tests */
    private $dispatcher = null;
    /** @var Application The application */
    private $application = null;
    /** @var Container The container to use in tests */
    private $container = null;
    /** @var BootstrapperRegistry The bootstrapper registry */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $paths = new Paths([]);
        $taskDispatcher = new TaskDispatcher();
        $environment = new Environment(Environment::TESTING);
        $this->container = new Container();
        $this->application = new Application($paths, $taskDispatcher, $environment, $this->container);
        $this->dispatcher = new Dispatcher($taskDispatcher, $this->container);
        $this->registry = new BootstrapperRegistry($paths, $environment);
    }

    /**
     * Tests dispatching all bootstrappers eagerly
     */
    public function testDispatchingAllBootstrappersEagerly()
    {
        $this->dispatcher->forceEagerLoading(true);
        $this->registry->registerEagerBootstrapper(EagerBootstrapper::class);
        $this->registry->registerLazyBootstrapper(LazyFooInterface::class, LazyBootstrapper::class);
        $this->dispatcher->dispatch($this->registry);
        $this->assertEquals(LazyConcreteFoo::class, $this->container->getBinding(LazyFooInterface::class));
        $this->assertEquals(EagerConcreteFoo::class, $this->container->getBinding(EagerFooInterface::class));
    }

    /**
     * Tests dispatching an eager bootstrapper that depends on a dependency set in a lazy bootstrapper
     */
    public function testDispatchingEagerDispatcherThatDependsOnDependencyFromLazyBootstrapper()
    {
        $this->registry->registerEagerBootstrapper(EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class);
        $this->registry->registerLazyBootstrapper(LazyFooInterface::class, LazyBootstrapper::class);
        ob_start();
        $this->dispatcher->dispatch($this->registry);
        $this->assertEquals(LazyConcreteFoo::class, ob_get_clean());
    }

    /**
     * Tests dispatching a lazy bootstrapper (registered first) that depends on a dependency set in a lazy bootstrapper
     */
    public function testDispatchingLazyDispatcherThatDependsOnDependencyFromLazyBootstrapperAndRegisteringItFirst()
    {
        $this->registry->registerLazyBootstrapper(
            EagerFooInterface::class,
            LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper::class
        );
        $this->registry->registerLazyBootstrapper(LazyFooInterface::class, LazyBootstrapper::class);
        ob_start();
        $this->dispatcher->dispatch($this->registry);
        $this->container->makeNew(EagerFooInterface::class);
        $this->assertEquals(LazyConcreteFoo::class, ob_get_clean());
    }

    /**
     * Tests dispatching a lazy bootstrapper (registered second) that depends on a dependency set in a lazy bootstrapper
     */
    public function testDispatchingLazyDispatcherThatDependsOnDependencyFromLazyBootstrapperAndRegisteringItSecond()
    {
        $this->registry->registerLazyBootstrapper(LazyFooInterface::class, LazyBootstrapper::class);
        $this->registry->registerLazyBootstrapper(
            EagerFooInterface::class,
            LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper::class
        );
        ob_start();
        $this->dispatcher->dispatch($this->registry);
        $this->container->makeNew(EagerFooInterface::class);
        $this->assertEquals(LazyConcreteFoo::class, ob_get_clean());
    }

    /**
     * Tests that eager bootstrappers are shutdown
     */
    public function testEagerBootstrappersAreShutdown()
    {
        $this->registry->registerEagerBootstrapper(EnvironmentBootstrapper::class);
        $this->dispatcher->dispatch($this->registry);
        $this->application->start();
        $this->assertEquals("running", $this->application->getEnvironment()->getName());
        $this->application->shutdown();
        $this->assertEquals("shutting down", $this->application->getEnvironment()->getName());
    }

    /**
     * Tests that lazy bootstrappers are shutdown
     */
    public function testLazyBootstrappersAreShutdown()
    {
        $this->registry->registerLazyBootstrapper(LazyFooInterface::class, EnvironmentBootstrapper::class);
        $this->dispatcher->dispatch($this->registry);
        $this->application->start();
        // Need to actual force the lazy bootstrapper to load
        $this->container->makeNew(LazyFooInterface::class);
        $this->assertEquals("running", $this->application->getEnvironment()->getName());
        $this->application->shutdown();
        $this->assertEquals("shutting down", $this->application->getEnvironment()->getName());
    }

    /**
     * Tests that a lazy bootstrapper's bindings are available to the container
     */
    public function testLazyBootstrappersBindingsAreAvailableToContainer()
    {
        $this->registry->registerLazyBootstrapper(LazyFooInterface::class, LazyBootstrapper::class);
        $this->dispatcher->dispatch($this->registry);
        $this->assertInstanceOf(Closure::class, $this->container->getBinding(LazyFooInterface::class));
        $this->assertInstanceOf(LazyConcreteFoo::class, $this->container->makeNew(LazyFooInterface::class));
    }

    /**
     * Tests not dispatching all bootstrappers eagerly
     */
    public function testNotDispatchingAllBootstrappersEagerly()
    {
        $this->registry->registerEagerBootstrapper(EagerBootstrapper::class);
        $this->registry->registerLazyBootstrapper(LazyFooInterface::class, LazyBootstrapper::class);
        $this->dispatcher->dispatch($this->registry);
        $this->assertInstanceOf(Closure::class, $this->container->getBinding(LazyFooInterface::class));
        $this->assertEquals(EagerConcreteFoo::class, $this->container->getBinding(EagerFooInterface::class));
    }
}