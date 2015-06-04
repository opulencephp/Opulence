<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Tests the bootstrapper reader/writer
 */
namespace RDev\Applications\Bootstrappers\IO;
use RDev\Applications\Bootstrappers\BootstrapperRegistry;
use RDev\Applications\Bootstrappers\ILazyBootstrapper;
use RDev\Applications\Environments\Environment;
use RDev\Applications\Paths;
use RDev\Tests\Applications\Bootstrappers\Mocks\EagerBootstrapper;
use RDev\Tests\Applications\Bootstrappers\Mocks\LazyBootstrapper;

class BootstrapperIOTest extends \PHPUnit_Framework_TestCase
{
    /** @var BootstrapperIO The reader/writer to use in tests */
    private $io = null;
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
        $this->io = new BootstrapperIO($this->paths, $this->environment);
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
     * Tests reading when there is no cached registry
     */
    public function testReadingWhenNoCachedRegistryExists()
    {
        $this->registry->registerBootstrapperClasses([EagerBootstrapper::class, LazyBootstrapper::class]);
        $this->io->read($this->cachedRegistryFilePath, $this->registry);
        $this->assertEquals([EagerBootstrapper::class], $this->registry->getEagerBootstrapperClasses());
        $this->assertEquals(
            $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class),
            $this->registry->getBindingsToLazyBootstrapperClasses()
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
        $this->io->read($this->cachedRegistryFilePath, $this->registry);
        $this->assertEquals([EagerBootstrapper::class], $this->registry->getEagerBootstrapperClasses());
        $this->assertEquals(
            $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class),
            $this->registry->getBindingsToLazyBootstrapperClasses()
        );
    }

    /**
     * Tests registering bootstrapper classes multiple times
     */
    public function testRegisteringBootstrapperClassesMultipleTimes()
    {
        $this->registry->registerBootstrapperClasses([EagerBootstrapper::class]);
        $this->registry->registerBootstrapperClasses([LazyBootstrapper::class]);
        $this->io->read($this->cachedRegistryFilePath, $this->registry);
        $this->assertEquals([EagerBootstrapper::class], $this->registry->getEagerBootstrapperClasses());
        $this->assertEquals(
            $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class),
            $this->registry->getBindingsToLazyBootstrapperClasses()
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
        $this->io->write($this->cachedRegistryFilePath, $registry);
        $this->io->read($this->cachedRegistryFilePath, $this->registry);
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
        $registry->registerLazyBootstrapper($lazyBootstrapper->getBoundClasses(), LazyBootstrapper::class);
        $this->io->write($this->cachedRegistryFilePath, $registry);
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
        $this->io->write($this->cachedRegistryFilePath, $registry);
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
        $this->io->write($this->cachedRegistryFilePath, $registry);
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