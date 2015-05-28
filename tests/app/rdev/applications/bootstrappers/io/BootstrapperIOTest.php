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
        $this->io = new BootstrapperIO($this->paths, $this->environment);
    }

    /**
     * Tears down the tests
     */
    public function tearDown()
    {
        if(file_exists($this->getCachedRegistryFilePath()))
        {
            @unlink($this->getCachedRegistryFilePath());
        }
    }

    /**
     * Tests reading when there is no cached registry
     */
    public function testReadingWhenNoCachedRegistryExists()
    {
        $this->io->registerBootstrapperClasses([EagerBootstrapper::class, LazyBootstrapper::class]);
        $registry = $this->io->read();
        $this->assertInstanceOf(IBootstrapperRegistry::class, $registry);
        $this->assertEquals([EagerBootstrapper::class], $registry->getEagerBootstrapperClasses());
        $this->assertEquals(
            $this->getBindingsToLazyBootstrappers(LazyBootstrapper::class),
            $registry->getBindingsToLazyBootstrapperClasses()
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
        $registry = $this->io->read();
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
        $registry = $this->io->read();
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
        $this->io->write($registry);
        $this->assertEquals($registry, $this->io->read());
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
        $this->io->write($registry);
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
        $this->io->write($registry);
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
        $this->io->write($registry);
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
     * Gets the cached registry file path
     *
     * @return string The cached registry file path
     */
    private function getCachedRegistryFilePath()
    {
        return $this->paths["tmp.framework"] . "/" . BootstrapperIO::CACHED_REGISTRY_FILE_NAME;
    }

    /**
     * Reads data from the cached registry file
     *
     * @return array The decoded data
     */
    private function readFromCachedRegistryFile()
    {
        return json_decode(file_get_contents($this->getCachedRegistryFilePath()), true);
    }

    /**
     * Writes data to the registry
     *
     * @param array $data The data to write
     */
    private function writeRegistry(array $data)
    {
        file_put_contents($this->getCachedRegistryFilePath(), json_encode($data));
    }
}