<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc\Bootstrappers;

use InvalidArgumentException;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\EagerBootstrapper;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\EagerFooInterface;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\LazyBootstrapper;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\LazyFooInterface;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\NonBootstrapper;
use RuntimeException;

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
     * Tests getting an instance of the bootstrapper
     */
    public function testGettingInstanceOfBootstrapper()
    {
        $this->assertInstanceOf(LazyBootstrapper::class, $this->registry->resolve(LazyBootstrapper::class));
    }

    /**
     * Tests that getting an instance of a class that is not a bootstrapper throws an exception
     */
    public function testGettingInstanceOfNonBootstrapperThrowsException()
    {
        $this->expectException(RuntimeException::class);
        $this->registry->resolve(NonBootstrapper::class);
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
                LazyFooInterface::class => ["bootstrapper" => LazyBootstrapper::class, "target" => null],
                EagerFooInterface::class => ["bootstrapper" => LazyBootstrapper::class, "target" => null]
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
                LazyFooInterface::class => ["bootstrapper" => LazyBootstrapper::class, "target" => null]
            ],
            $this->registry->getLazyBootstrapperBindings()
        );
    }

    /**
     * Tests registering and getting a lazy bootstrapper with a single targeted binding
     */
    public function testRegisteringAndGettingLazyBootstrapperWithSingleTargetedBinding()
    {
        $this->registry->registerLazyBootstrapper([["foo" => "bar"]], "baz");
        $this->assertEquals(
            [
                "foo" => ["bootstrapper" => "baz", "target" => "bar"]
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
            $lazyBootstrapper = new $lazyBootstrapperClass();

            foreach ($lazyBootstrapper->getBindings() as $boundClass) {
                $targetClass = null;

                if (is_array($boundClass)) {
                    $targetClass = array_values($boundClass)[0];
                    $boundClass = array_keys($boundClass)[0];
                }

                $bindingsToLazyBootstrappers[$boundClass] = [
                    "bootstrapper" => $lazyBootstrapperClass,
                    "target" => $targetClass
                ];
            }
        }

        return $bindingsToLazyBootstrappers;
    }
}