<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the bootstrapper cache
 */
namespace Opulence\Applications\Bootstrappers\Caching;
use Opulence\Applications\Bootstrappers\BootstrapperRegistry;
use Opulence\Applications\Bootstrappers\ILazyBootstrapper;
use Opulence\Applications\Environments\Environment;
use Opulence\Applications\Paths;
use Opulence\Tests\Applications\Bootstrappers\Mocks\EagerBootstrapper;
use Opulence\Tests\Applications\Bootstrappers\Mocks\LazyBootstrapper;

class CacheTest extends \PHPUnit_Framework_TestCase
{
    /** @var Cache The cache to use in tests */
    private $cache = null;
    /** @var BootstrapperRegistry The registry to use in tests */
    private $registry = null;
    /** @var Paths The application paths */
    private $paths = null;
    /** @var Environment The current environment */
    private $environment = null;
    /** @var string The path to the cached registry file */
    private $cachedRegistryFilePath = "";

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->cachedRegistryFilePath = __DIR__ . "/cachedRegistry.json";
        $this->paths = new Paths([
            "tmp.framework" => __DIR__ . "/files"
        ]);
        $this->environment = new Environment(Environment::TESTING);
        $this->cache = new Cache();
        $this->registry = new BootstrapperRegistry($this->paths, $this->environment);
    }

    /**
     * Tears down the tests
     */
    public function tearDown()
    {
        if(file_exists(($this->cachedRegistryFilePath)))
        {
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
        $lazyBootstrapper = new LazyBootstrapper($this->paths, $this->environment);
        $registry = new BootstrapperRegistry($this->paths, $this->environment);
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
        $lazyBootstrapper = new LazyBootstrapper($this->paths, $this->environment);
        $registry = new BootstrapperRegistry($this->paths, $this->environment);
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
        $lazyBootstrapper = new LazyBootstrapper($this->paths, $this->environment);
        $registry = new BootstrapperRegistry($this->paths, $this->environment);
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
        $registry = new BootstrapperRegistry($this->paths, $this->environment);
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

        foreach($lazyBootstrapperClasses as $lazyBootstrapperClass)
        {
            /** @var ILazyBootstrapper $lazyBootstrapper */
            $lazyBootstrapper = new $lazyBootstrapperClass($this->paths, $this->environment);

            foreach($lazyBootstrapper->getBindings() as $boundClass)
            {
                $bindingsToLazyBootstrappers[$boundClass] = LazyBootstrapper::class;
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