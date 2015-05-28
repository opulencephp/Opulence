<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the bootstrapper registry
 */
namespace RDev\Applications\Bootstrappers;
use RDev\Applications\Environments\Environment;
use RDev\Applications\Paths;
use RDev\Tests\Applications\Bootstrappers\Mocks\EagerBootstrapper;
use RDev\Tests\Applications\Bootstrappers\Mocks\EagerFooInterface;
use RDev\Tests\Applications\Bootstrappers\Mocks\LazyBootstrapper;
use RDev\Tests\Applications\Bootstrappers\Mocks\LazyFooInterface;
use RDev\Tests\Applications\Bootstrappers\Mocks\NonBootstrapper;
use RuntimeException;

class BootstrapperRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var BootstrapperRegistry The registry to test */
    private $registry = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->registry = new BootstrapperRegistry(new Paths([]), new Environment(Environment::TESTING));
    }

    /**
     * Tests getting an instance of the bootstrapper
     */
    public function testGettingInstanceOfBootstrapper()
    {
        $this->assertInstanceOf(LazyBootstrapper::class, $this->registry->getInstance(LazyBootstrapper::class));
    }

    /**
     * Tests that getting an instance of a class that is not a bootstrapper throws an exception
     */
    public function testGettingInstanceOfNonBootstrapperThrowsException()
    {
        $this->setExpectedException(RuntimeException::class);
        $this->registry->getInstance(NonBootstrapper::class);
    }

    /**
     * Tests registering and getting an eager bootstrapper
     */
    public function testRegisteringAndGettingEagerBootstrapper()
    {
        $this->registry->registerEagerBootstrapper(EagerBootstrapper::class);
        $this->assertEquals([EagerBootstrapper::class], $this->registry->getEagerBootstrapperClasses());
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
                LazyFooInterface::class => LazyBootstrapper::class,
                EagerFooInterface::class => LazyBootstrapper::class
            ],
            $this->registry->getBindingsToLazyBootstrapperClasses()
        );
    }

    /**
     * Tests registering and getting a lazy bootstrapper with a single binding
     */
    public function testRegisteringAndGettingLazyBootstrapperWithSingleBinding()
    {
        $this->registry->registerLazyBootstrapper(LazyFooInterface::class, LazyBootstrapper::class);
        $this->assertEquals(
            [
                LazyFooInterface::class => LazyBootstrapper::class
            ],
            $this->registry->getBindingsToLazyBootstrapperClasses()
        );
    }
}