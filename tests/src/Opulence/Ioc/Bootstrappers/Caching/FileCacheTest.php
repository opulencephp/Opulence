<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc\Bootstrappers\Caching;

use Opulence\Ioc\Bootstrappers\BootstrapperRegistry;
use Opulence\Ioc\Bootstrappers\ILazyBootstrapper;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\EagerBootstrapper;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\LazyBootstrapper;
use Opulence\Tests\Ioc\Bootstrappers\Mocks\LazyBootstrapperWithTargetedBinding;

/**
 * Tests the bootstrapper file cache
 */
class FileCacheTest extends \PHPUnit\Framework\TestCase
{
    /** @var FileCache The cache to use in tests */
    private $cache = null;
    /** @var BootstrapperRegistry The registry to use in tests */
    private $registry = null;
    /** @var string The path to the cached registry file */
    private $cachedRegistryFilePath = "";

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->cachedRegistryFilePath = __DIR__ . "/cachedRegistry.json";
        $this->cache = new FileCache();
        $this->registry = new BootstrapperRegistry();
    }

    /**
     * Tears down the tests
     */
    public function tearDown()
    {
        if (file_exists(($this->cachedRegistryFilePath))) {
            @unlink($this->cachedRegistryFilePath);
        }
    }

    /**
     * Tests flushing the cache
     */
    public function testFlushing()
    {
        file_put_contents($this->cachedRegistryFilePath, "foo");
        $this->cache->flush($this->cachedRegistryFilePath);
        $this->assertFalse(file_exists($this->cachedRegistryFilePath));
    }

    /**
     * Tests reading when there is no cached registry
     */
    public function testReadingWhenNoCachedRegistryExists()
    {
        $this->registry->registerBootstrappers([EagerBootstrapper::class, LazyBootstrapper::class]);
        $this->cache->get($this->cachedRegistryFilePath, $this->registry);
        $this->assertEquals([EagerBootstrapper::class], $this->registry->getEagerBootstrappers());
        $this->assertEquals(
            $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class),
            $this->registry->getLazyBootstrapperBindings()
        );
        // Make sure that the information was cached
        $this->assertEquals(
            [
                "eager" => [EagerBootstrapper::class],
                "lazy" => $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class)
            ],
            $this->readFromCachedRegistryFile()
        );
    }

    /**
     * Tests reading when there is a cached registry
     */
    public function testReadingWithCachedRegistryExists()
    {
        $this->writeRegistry([
            "eager" => [EagerBootstrapper::class],
            "lazy" => $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class)
        ]);
        $this->cache->get($this->cachedRegistryFilePath, $this->registry);
        $this->assertEquals([EagerBootstrapper::class], $this->registry->getEagerBootstrappers());
        $this->assertEquals(
            $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class),
            $this->registry->getLazyBootstrapperBindings()
        );
    }

    /**
     * Tests registering bootstrapper classes multiple times
     */
    public function testRegisteringBootstrapperClassesMultipleTimes()
    {
        $this->registry->registerBootstrappers([EagerBootstrapper::class]);
        $this->registry->registerBootstrappers([LazyBootstrapper::class]);
        $this->cache->get($this->cachedRegistryFilePath, $this->registry);
        $this->assertEquals([EagerBootstrapper::class], $this->registry->getEagerBootstrappers());
        $this->assertEquals(
            $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class),
            $this->registry->getLazyBootstrapperBindings()
        );
    }

    /**
     * Tests writing a registry and then reading from it
     */
    public function testWritingAndThenReadingRegistry()
    {
        $lazyBootstrapper = new LazyBootstrapper();
        $registry = new BootstrapperRegistry();
        $registry->registerEagerBootstrapper(EagerBootstrapper::class);
        $registry->registerLazyBootstrapper($lazyBootstrapper->getBindings(), LazyBootstrapper::class);
        $this->cache->set($this->cachedRegistryFilePath, $registry);
        $this->cache->get($this->cachedRegistryFilePath, $this->registry);
        $this->assertEquals($registry, $this->registry);
    }

    /**
     * Tests writing a registry with targeted binding and then reading from it
     */
    public function testWritingAndThenReadingRegistryWithTargetedBinding()
    {
        $lazyBootstrapper = new LazyBootstrapperWithTargetedBinding();
        $registry = new BootstrapperRegistry();
        $registry->registerEagerBootstrapper(EagerBootstrapper::class);
        $registry->registerLazyBootstrapper($lazyBootstrapper->getBindings(), LazyBootstrapper::class);
        $this->cache->set($this->cachedRegistryFilePath, $registry);
        $this->cache->get($this->cachedRegistryFilePath, $this->registry);
        $this->assertEquals($registry, $this->registry);
    }

    /**
     * Tests writing a registry
     */
    public function testWritingRegistry()
    {
        $lazyBootstrapper = new LazyBootstrapper();
        $registry = new BootstrapperRegistry();
        $registry->registerEagerBootstrapper(EagerBootstrapper::class);
        $registry->registerLazyBootstrapper($lazyBootstrapper->getBindings(), LazyBootstrapper::class);
        $this->cache->set($this->cachedRegistryFilePath, $registry);
        $this->assertEquals(
            [
                "eager" => [EagerBootstrapper::class],
                "lazy" => $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class)
            ],
            $this->readFromCachedRegistryFile()
        );
    }

    /**
     * Tests writing a registry with no eager bootstrappers
     */
    public function testWritingRegistryWithNoEagerBootstrappers()
    {
        $lazyBootstrapper = new LazyBootstrapper();
        $registry = new BootstrapperRegistry();
        $registry->registerLazyBootstrapper($lazyBootstrapper->getBindings(), LazyBootstrapper::class);
        $this->cache->set($this->cachedRegistryFilePath, $registry);
        $this->assertEquals(
            [
                "eager" => [],
                "lazy" => $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class)
            ],
            $this->readFromCachedRegistryFile()
        );
    }

    /**
     * Tests writing a registry with no lazy bootstrappers
     */
    public function testWritingRegistryWithNoLazyBootstrappers()
    {
        $registry = new BootstrapperRegistry();
        $registry->registerEagerBootstrapper(EagerBootstrapper::class);
        $this->cache->set($this->cachedRegistryFilePath, $registry);
        $this->assertEquals(
            [
                "eager" => [EagerBootstrapper::class],
                "lazy" => []
            ],
            $this->readFromCachedRegistryFile()
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

    /**
     * Reads data from the cached registry file
     *
     * @return array The decoded data
     */
    private function readFromCachedRegistryFile()
    {
        return json_decode(file_get_contents($this->cachedRegistryFilePath), true);
    }

    /**
     * Writes data to the registry
     *
     * @param array $data The data to write
     */
    private function writeRegistry(array $data)
    {
        file_put_contents($this->cachedRegistryFilePath, json_encode($data));
    }
}