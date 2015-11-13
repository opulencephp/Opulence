<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Bootstrappers;

use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Paths;
use Opulence\Tests\Bootstrappers\Mocks\EagerBootstrapper;
use Opulence\Tests\Bootstrappers\Mocks\EagerFooInterface;
use Opulence\Tests\Bootstrappers\Mocks\LazyBootstrapper;
use Opulence\Tests\Bootstrappers\Mocks\LazyFooInterface;
use Opulence\Tests\Bootstrappers\Mocks\NonBootstrapper;
use RuntimeException;

/**
 * Tests the bootstrapper registry
 */
class BootstrapperRegistryTest extends \PHPUnit_Framework_TestCase
{
    /** @var BootstrapperRegistry The registry to test */
    private $registry = null;
    /** @var Paths The application paths */
    private $paths = null;
    /** @var Environment The current environment */
    private $environment = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->paths = new Paths([]);
        $this->environment = new Environment(Environment::TESTING);
        $this->registry = new BootstrapperRegistry($this->paths, $this->environment);
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
     * Tests loading a registry from the bootstrapper classes
     */
    public function testLoadingRegistryFromBootstrapperClasses()
    {
        $this->registry->registerBootstrappers([EagerBootstrapper::class, LazyBootstrapper::class]);
        $this->registry->setBootstrapperDetails();
        $this->assertEquals([EagerBootstrapper::class], $this->registry->getEagerBootstrappers());
        $this->assertEquals(
            $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class),
            $this->registry->getLazyBootstrapperBindings()
        );
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
                LazyFooInterface::class => LazyBootstrapper::class,
                EagerFooInterface::class => LazyBootstrapper::class
            ],
            $this->registry->getLazyBootstrapperBindings()
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
            $this->registry->getLazyBootstrapperBindings()
        );
    }

    /**
     * Gets the bindings to lazy bootstrapper class mappings
     *
     * @param string|array $lazyBootstrapperClasses The lazy bootstrapper to create
     * @return array The bindings to lazy bootstrappers
     */
    private function getBindingsToLazyBootstrappers($lazyBootstrapperClasses)
    {
        $lazyBootstrapperClasses = (array)$lazyBootstrapperClasses;
        $bindingsToLazyBootstrappers = [];

        foreach ($lazyBootstrapperClasses as $lazyBootstrapperClass) {
            /** @var ILazyBootstrapper $lazyBootstrapper */
            $lazyBootstrapper = new $lazyBootstrapperClass($this->paths, $this->environment);

            foreach ($lazyBootstrapper->getBindings() as $boundClass) {
                $bindingsToLazyBootstrappers[$boundClass] = LazyBootstrapper::class;
            }
        }

        return $bindingsToLazyBootstrappers;
    }
}