<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Bootstrappers\Dispatchers;

use Closure;
use Opulence\Ioc\Container;
use Opulence\Applications\Application;
use Opulence\Applications\Tasks\Dispatchers\Dispatcher as TaskDispatcher;
use Opulence\Bootstrappers\BootstrapperRegistry;
use Opulence\Bootstrappers\Paths;
use Opulence\Environments\Environment;
use Opulence\Ioc\IocException;
use Opulence\Tests\Bootstrappers\Mocks\BootstrapperWithEverything;
use Opulence\Tests\Bootstrappers\Mocks\EagerBootstrapper;
use Opulence\Tests\Bootstrappers\Mocks\EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper;
use Opulence\Tests\Bootstrappers\Mocks\EagerFooInterface;
use Opulence\Tests\Bootstrappers\Mocks\EagerConcreteFoo;
use Opulence\Tests\Bootstrappers\Mocks\EnvironmentBootstrapper;
use Opulence\Tests\Bootstrappers\Mocks\LazyBootstrapper;
use Opulence\Tests\Bootstrappers\Mocks\LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper;
use Opulence\Tests\Bootstrappers\Mocks\LazyBootstrapperWithTargetedBinding;
use Opulence\Tests\Bootstrappers\Mocks\LazyConcreteFoo;
use Opulence\Tests\Bootstrappers\Mocks\LazyFooInterface;

/**
 * Tests the bootstrapper dispatcher
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Dispatcher The dispatcher to use in tests */
    private $dispatcher = null;
    /** @var Application The application */
    private $application = null;
    /** @var Environment The current environment */
    private $environment = null;
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
        $this->environment = new Environment(Environment::TESTING);
        $this->container = new Container();
        $this->application = new Application($taskDispatcher);
        $this->dispatcher = new Dispatcher($taskDispatcher, $this->container);
        $this->registry = new BootstrapperRegistry($paths, $this->environment);
    }

    /**
     * Tests that bootstrapper methods are called in correct order
     */
    public function testBootstrapperMethodsCalledInCorrectOrder()
    {
        $this->dispatcher->forceEagerLoading(true);
        $clonedRegistry = clone $this->registry;
        $this->registry->registerEagerBootstrapper(BootstrapperWithEverything::class);
        ob_start();
        $this->dispatcher->dispatch($this->registry);
        $this->assertEquals("initializeregisterBindingsrun", ob_get_clean());
        $this->dispatcher->forceEagerLoading(false);
        $clonedRegistry->registerLazyBootstrapper([LazyFooInterface::class], BootstrapperWithEverything::class);
        ob_start();
        $this->dispatcher->dispatch($clonedRegistry);
        $this->container->makeShared(LazyFooInterface::class);
        $this->assertEquals("initializeregisterBindingsrun", ob_get_clean());
    }

    /**
     * Tests dispatching all bootstrappers eagerly
     */
    public function testDispatchingAllBootstrappersEagerly()
    {
        $this->dispatcher->forceEagerLoading(true);
        $this->registry->registerEagerBootstrapper(EagerBootstrapper::class);
        $this->registry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
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
        $this->registry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
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
            [EagerFooInterface::class],
            LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper::class
        );
        $this->registry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
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
        $this->registry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
        $this->registry->registerLazyBootstrapper(
            [EagerFooInterface::class],
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
        $this->assertEquals("running", $this->environment->getName());
        $this->application->shutDown();
        $this->assertEquals("shutting down", $this->environment->getName());
    }

    /**
     * Tests that lazy bootstrappers are shutdown
     */
    public function testLazyBootstrappersAreShutdown()
    {
        $this->registry->registerLazyBootstrapper([LazyFooInterface::class], EnvironmentBootstrapper::class);
        $this->dispatcher->dispatch($this->registry);
        $this->application->start();
        // Need to actual force the lazy bootstrapper to load
        $this->container->makeNew(LazyFooInterface::class);
        $this->assertEquals("running", $this->environment->getName());
        $this->application->shutDown();
        $this->assertEquals("shutting down", $this->environment->getName());
    }

    /**
     * Tests that a lazy bootstrapper's bindings are available to the container
     */
    public function testLazyBootstrappersBindingsAreAvailableToContainer()
    {
        $this->registry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
        $this->dispatcher->dispatch($this->registry);
        $this->assertInstanceOf(Closure::class, $this->container->getBinding(LazyFooInterface::class));
        $this->assertInstanceOf(LazyConcreteFoo::class, $this->container->makeNew(LazyFooInterface::class));
    }

    /**
     * Tests that lazy targeted bindings are available in the container
     */
    public function testLazyTargetedBindingsAreAvailableInContainer()
    {
        $this->registry->registerLazyBootstrapper(
            [[LazyFooInterface::class => EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class]],
            LazyBootstrapperWithTargetedBinding::class
        );
        $this->dispatcher->dispatch($this->registry);
        // Make sure it's bound to the target
        $this->assertInstanceOf(
            Closure::class,
            $this->container->getBinding(
                LazyFooInterface::class,
                EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class
            )
        );
        $this->assertInstanceOf(
            LazyConcreteFoo::class,
            $this->container->makeShared(
                LazyFooInterface::class,
                EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class
            )
        );
        // Make sure it wasn't bound universally
        $notBoundUniversally = false;
        try {
            $this->assertInstanceOf(LazyConcreteFoo::class, $this->container->makeShared(LazyFooInterface::class));
        } catch (IocException $ex) {
            $notBoundUniversally = true;
        }
        $this->assertTrue($notBoundUniversally);
        $this->assertNull($this->container->getBinding(LazyFooInterface::class));
    }

    /**
     * Tests not dispatching all bootstrappers eagerly
     */
    public function testNotDispatchingAllBootstrappersEagerly()
    {
        $this->registry->registerEagerBootstrapper(EagerBootstrapper::class);
        $this->registry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
        $this->dispatcher->dispatch($this->registry);
        $this->assertInstanceOf(Closure::class, $this->container->getBinding(LazyFooInterface::class));
        $this->assertEquals(EagerConcreteFoo::class, $this->container->getBinding(EagerFooInterface::class));
    }
}