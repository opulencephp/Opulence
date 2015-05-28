<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the bootstrapper dispatcher
 */
namespace RDev\Applications\Bootstrappers\Dispatchers;
use Closure;
use Monolog\Logger;
use RDev\IoC\Container;
use RDev\Applications\Application;
use RDev\Applications\Bootstrappers\BootstrapperRegistry;
use RDev\Applications\Environments\Environment;
use RDev\Applications\Paths;
use RDev\Tests\Applications\Bootstrappers\Mocks\EagerBootstrapper;
use RDev\Tests\Applications\Bootstrappers\Mocks\EagerConcreteFoo;
use RDev\Tests\Applications\Bootstrappers\Mocks\EagerFooInterface;
use RDev\Tests\Applications\Bootstrappers\Mocks\EnvironmentBootstrapper;
use RDev\Tests\Applications\Bootstrappers\Mocks\LazyFooInterface;
use RDev\Tests\Applications\Bootstrappers\Mocks\LazyBootstrapper;
use RDev\Tests\Applications\Bootstrappers\Mocks\LazyConcreteFoo;
use RDev\Tests\Applications\Mocks\MonologHandler;

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
        $logger = new Logger("application");
        $logger->pushHandler(new MonologHandler());
        $paths = new Paths([]);
        $environment = new Environment(Environment::TESTING);
        $this->container = new Container();
        $this->application = new Application($paths, $logger, $environment, $this->container);
        $this->dispatcher = new Dispatcher($this->application);
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