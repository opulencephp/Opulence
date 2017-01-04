<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc\Bootstrappers\Dispatchers;

use Opulence\Ioc\Bootstrappers\BootstrapperRegistry;
use Opulence\Ioc\Bootstrappers\IBootstrapperResolver;
use Opulence\Ioc\Container;
use Opulence\Ioc\IContainer;
use Opulence\Ioc\IocException;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\BootstrapperWithEverything;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\EagerBootstrapper;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\EagerConcreteFoo;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\EagerFooInterface;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\EnvironmentBootstrapper;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\LazyBootstrapper;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\LazyBootstrapperWithTargetedBinding;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\LazyConcreteFoo;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\LazyFooInterface;

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
    /** @var IBootstrapperResolver|\PHPUnit_Framework_MockObject_MockObject The bootstrapper resolver */
    private $bootstrapperResolver = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->container = new Container();
        $this->bootstrapperRegistry = new BootstrapperRegistry();
        $this->bootstrapperResolver = $this->createMock(IBootstrapperResolver::class);
        $this->bootstrapperResolver->expects($this->any())
            ->method("resolve")
            ->willReturnCallback(function () {
                $bootstrapperClass = func_get_arg(0);

                return new $bootstrapperClass();
            });
        $this->dispatcher = new BootstrapperDispatcher(
            $this->container,
            $this->bootstrapperRegistry,
            $this->bootstrapperResolver
        );
    }

    /**
     * Tests dispatching all bootstrappers eagerly
     */
    public function testDispatchingAllBootstrappersEagerly()
    {
        $this->bootstrapperRegistry->registerEagerBootstrapper(EagerBootstrapper::class);
        $this->bootstrapperRegistry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
        $this->dispatcher->startBootstrappers(true);
        $this->assertInstanceOf(LazyConcreteFoo::class, $this->container->resolve(LazyFooInterface::class));
        $this->assertInstanceOf(EagerConcreteFoo::class, $this->container->resolve(EagerFooInterface::class));
    }

    /**
     * Tests dispatching an eager bootstrapper that depends on a dependency set in a lazy bootstrapper
     */
    public function testDispatchingEagerDispatcherThatDependsOnDependencyFromLazyBootstrapper()
    {
        $this->bootstrapperRegistry->registerEagerBootstrapper(EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class);
        $this->bootstrapperRegistry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
        ob_start();
        $this->dispatcher->startBootstrappers(false);
        $this->assertEquals(LazyConcreteFoo::class, ob_get_clean());
    }

    /**
     * Tests dispatching a lazy bootstrapper (registered first) that depends on a dependency set in a lazy bootstrapper
     */
    public function testDispatchingLazyDispatcherThatDependsOnDependencyFromLazyBootstrapperAndRegisteringItFirst()
    {
        $this->bootstrapperRegistry->registerLazyBootstrapper(
            [EagerFooInterface::class],
            LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper::class
        );
        $this->bootstrapperRegistry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
        ob_start();
        $this->dispatcher->startBootstrappers(false);
        $this->container->resolve(EagerFooInterface::class);
        $this->assertEquals(LazyConcreteFoo::class, ob_get_clean());
    }

    /**
     * Tests dispatching a lazy bootstrapper (registered second) that depends on a dependency set in a lazy bootstrapper
     */
    public function testDispatchingLazyDispatcherThatDependsOnDependencyFromLazyBootstrapperAndRegisteringItSecond()
    {
        $this->bootstrapperRegistry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
        $this->bootstrapperRegistry->registerLazyBootstrapper(
            [EagerFooInterface::class],
            LazyBootstrapperThatDependsOnBindingFromLazyBootstrapper::class
        );
        ob_start();
        $this->dispatcher->startBootstrappers(false);
        $this->container->resolve(EagerFooInterface::class);
        $this->assertEquals(LazyConcreteFoo::class, ob_get_clean());
    }

    /**
     * Tests that eager bootstrappers' methods are called in correct order
     */
    public function testEagerBootstrapperMethodsCalledInCorrectOrder()
    {
        $this->bootstrapperRegistry->registerEagerBootstrapper(BootstrapperWithEverything::class);
        ob_start();
        $this->dispatcher->startBootstrappers(true);
        $this->assertEquals("registerBindingsrun", ob_get_clean());
    }

    /**
     * Tests that eager bootstrappers are shutdown
     */
    public function testEagerBootstrappersAreShutdown()
    {
        $this->bootstrapperRegistry->registerEagerBootstrapper(EnvironmentBootstrapper::class);
        $this->dispatcher->startBootstrappers(false);
        $this->assertEquals("running", getenv("TEST_ENV_NAME"));
        $this->dispatcher->shutDownBootstrappers();
        $this->assertEquals("shutdown", getenv("TEST_ENV_NAME"));
    }

    /**
     * Tests that lazy bootstrappers' methods are called in correct order
     */
    public function testLazyBootstrapperMethodsCalledInCorrectOrder()
    {
        $this->bootstrapperRegistry->registerLazyBootstrapper([LazyFooInterface::class],
            BootstrapperWithEverything::class);
        ob_start();
        $this->dispatcher->startBootstrappers(false);
        $this->container->resolve(LazyFooInterface::class);
        $this->assertEquals("registerBindingsrun", ob_get_clean());
    }

    /**
     * Tests that lazy bootstrappers are shutdown
     */
    public function testLazyBootstrappersAreShutdown()
    {
        $this->bootstrapperRegistry->registerLazyBootstrapper([LazyFooInterface::class],
            EnvironmentBootstrapper::class);
        $this->dispatcher->startBootstrappers(false);
        // Need to actually force the lazy bootstrapper to load
        $this->container->resolve(LazyFooInterface::class);
        $this->assertEquals("running", getenv("TEST_ENV_NAME"));
        $this->dispatcher->shutDownBootstrappers();
        $this->assertEquals("shutdown", getenv("TEST_ENV_NAME"));
    }

    /**
     * Tests that a lazy bootstrapper's bindings are available to the container
     */
    public function testLazyBootstrappersBindingsAreAvailableToContainer()
    {
        $this->bootstrapperRegistry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
        $this->dispatcher->startBootstrappers(false);
        $this->assertInstanceOf(LazyConcreteFoo::class, $this->container->resolve(LazyFooInterface::class));
    }

    /**
     * Tests that lazy targeted bindings are available in the container
     */
    public function testLazyTargetedBindingsAreAvailableInContainer()
    {
        $this->bootstrapperRegistry->registerLazyBootstrapper(
            [[LazyFooInterface::class => EagerBootstrapperThatDependsOnBindingFromLazyBootstrapper::class]],
            LazyBootstrapperWithTargetedBinding::class
        );
        $this->dispatcher->startBootstrappers(false);
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
            $this->fail("Targeted binding is accidentally universal");
        } catch (IocException $ex) {
            $notBoundUniversally = true;
        }

        $this->assertTrue($notBoundUniversally);
        $this->assertFalse($this->container->hasBinding(LazyFooInterface::class));
    }

    /**
     * Tests not dispatching all bootstrappers eagerly
     */
    public function testNotDispatchingAllBootstrappersEagerly()
    {
        $this->bootstrapperRegistry->registerEagerBootstrapper(EagerBootstrapper::class);
        $this->bootstrapperRegistry->registerLazyBootstrapper([LazyFooInterface::class], LazyBootstrapper::class);
        $this->dispatcher->startBootstrappers(false);
        $this->assertTrue($this->container->hasBinding(LazyFooInterface::class));
        $this->assertInstanceOf(EagerConcreteFoo::class, $this->container->resolve(EagerFooInterface::class));
    }
}
