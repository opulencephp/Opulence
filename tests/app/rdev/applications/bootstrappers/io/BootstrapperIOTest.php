<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the bootstrapper reader/writer
 */
namespace RDev\Applications\Bootstrappers\IO;
use RDev\Applications\Bootstrappers\BootstrapperRegistry;
use RDev\Applications\Bootstrappers\IBootstrapperRegistry;
use RDev\Applications\Bootstrappers\ILazyBootstrapper;
use RDev\Applications\Environments\Environment;
use RDev\Applications\Paths;
use RDev\Tests\Applications\Bootstrappers\Mocks\EagerBootstrapper;
use RDev\Tests\Applications\Bootstrappers\Mocks\LazyBootstrapper;

class BootstrapperIOTest extends \PHPUnit_Framework_TestCase
{
    const CACHED_REGISTRY_FILE_NAME = "cachedRegistry.json";

    /** @var BootstrapperIO The reader/writer to use in tests */
    private $io = null;
    /** @var Paths The application paths */
    private $paths = null;
    /** @var Environment The current environment */
    private $environment = null;

    /**
     * Sets up the tests
     */
    public function setUp()
    {
        $this->paths = new Paths([
            "tmp.framework" => __DIR__ . "/files"
        ]);
        $this->environment = new Environment(Environment::TESTING);
        $this->io = new BootstrapperIO($this->paths["tmp.framework"], $this->paths, $this->environment);
    }

    /**
     * Tears down the tests
     */
    public function tearDown()
    {
        if(file_exists($this->io->getCachedRegistryPath(self::CACHED_REGISTRY_FILE_NAME)))
        {
            @unlink($this->io->getCachedRegistryPath(self::CACHED_REGISTRY_FILE_NAME));
        }
    }

    /**
     * Tests getting the cached registry file name
     */
    public function testGettingCachedRegistryFileName()
    {
        $this->assertEquals(
            "{$this->paths["tmp.framework"]}/" . self::CACHED_REGISTRY_FILE_NAME,
            $this->io->getCachedRegistryPath(self::CACHED_REGISTRY_FILE_NAME)
        );
    }

    /**
     * Tests reading when there is no cached registry
     */
    public function testReadingWhenNoCachedRegistryExists()
    {
        $this->io->registerBootstrapperClasses([EagerBootstrapper::class, LazyBootstrapper::class]);
        $registry = $this->io->read(self::CACHED_REGISTRY_FILE_NAME);
        $this->assertInstanceOf(IBootstrapperRegistry::class, $registry);
        $this->assertEquals([EagerBootstrapper::class], $registry->getEagerBootstrapperClasses());
        $this->assertEquals(
            $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class),
            $registry->getBindingsToLazyBootstrapperClasses()
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
        $registry = $this->io->read(self::CACHED_REGISTRY_FILE_NAME);
        $this->assertInstanceOf(IBootstrapperRegistry::class, $registry);
        $this->assertEquals([EagerBootstrapper::class], $registry->getEagerBootstrapperClasses());
        $this->assertEquals(
            $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class),
            $registry->getBindingsToLazyBootstrapperClasses()
        );
    }

    /**
     * Tests registering bootstrapper classes multiple times
     */
    public function testRegisteringBootstrapperClassesMultipleTimes()
    {
        $this->io->registerBootstrapperClasses([EagerBootstrapper::class]);
        $this->io->registerBootstrapperClasses([LazyBootstrapper::class]);
        $registry = $this->io->read(self::CACHED_REGISTRY_FILE_NAME);
        $this->assertInstanceOf(IBootstrapperRegistry::class, $registry);
        $this->assertEquals([EagerBootstrapper::class], $registry->getEagerBootstrapperClasses());
        $this->assertEquals(
            $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class),
            $registry->getBindingsToLazyBootstrapperClasses()
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
        $registry->registerLazyBootstrapper($lazyBootstrapper->getBoundClasses(), LazyBootstrapper::class);
        $this->io->write(self::CACHED_REGISTRY_FILE_NAME, $registry);
        $this->assertEquals($registry, $this->io->read(self::CACHED_REGISTRY_FILE_NAME));
    }

    /**
     * Tests writing a registry
     */
    public function testWritingRegistry()
    {
        $lazyBootstrapper = new LazyBootstrapper($this->paths, $this->environment);
        $registry = new BootstrapperRegistry($this->paths, $this->environment);
        $registry->registerEagerBootstrapper(EagerBootstrapper::class);
        $registry->registerLazyBootstrapper($lazyBootstrapper->getBoundClasses(), LazyBootstrapper::class);
        $this->io->write(self::CACHED_REGISTRY_FILE_NAME, $registry);
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
        $registry->registerLazyBootstrapper($lazyBootstrapper->getBoundClasses(), LazyBootstrapper::class);
        $this->io->write(self::CACHED_REGISTRY_FILE_NAME, $registry);
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
        $this->io->write(self::CACHED_REGISTRY_FILE_NAME, $registry);
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

            foreach($lazyBootstrapper->getBoundClasses() as $boundClass)
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
        return json_decode(file_get_contents($this->io->getCachedRegistryPath(self::CACHED_REGISTRY_FILE_NAME)), true);
    }

    /**
     * Writes data to the registry
     *
     * @param array $data The data to write
     */
    private function writeRegistry(array $data)
    {
        file_put_contents($this->io->getCachedRegistryPath(self::CACHED_REGISTRY_FILE_NAME), json_encode($data));
    }
}