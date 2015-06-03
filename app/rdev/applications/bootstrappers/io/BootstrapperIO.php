<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the bootstrapper reader/writer
 */
namespace RDev\Applications\Bootstrappers\IO;
use RDev\Applications\Bootstrappers\BootstrapperRegistry;
use RDev\Applications\Bootstrappers\IBootstrapperRegistry;
use RDev\Applications\Bootstrappers\ILazyBootstrapper;
use RDev\Applications\Environments\Environment;
use RDev\Applications\Paths;

class BootstrapperIO
{
    /** The cached console bootstrapper registry file name */
    const CACHED_CONSOLE_BOOTSTRAPPER_REGISTRY_FILE_NAME = "cachedConsoleBootstrapperRegistry.json";
    /** The cached HTTP bootstrapper registry file name */
    const CACHED_HTTP_BOOTSTRAPPER_REGISTRY_FILE_NAME = "cachedHTTPBootstrapperRegistry.json";

    /** @var string The path to the cached registries */
    private $path = "";
    /** @var Paths The application paths */
    private $paths = null;
    /** @var Environment The current environment */
    private $environment = null;
    /** @var array The list of all bootstrapper classes in the application */
    private $allBootstrappers = [];

    /**
     * @param string $path The path to the cached registries
     * @param Paths $paths The application paths
     * @param Environment $environment The current environment
     */
    public function __construct($path, Paths $paths, Environment $environment)
    {
        $this->path = $path;
        $this->paths = $paths;
        $this->environment = $environment;
    }

    /**
     * Gets the cached registry file name
     *
     * @param string $fileName The filename
     * @return string The cached registry file name
     */
    public function getCachedRegistryPath($fileName)
    {
        return "{$this->path}/$fileName";
    }

    /**
     * Reads the bootstrapper registry from cache, if it exists, otherwise it generates a registry from the list of
     * bootstrappers
     *
     * @param string $fileName The cache registry filename
     * @return IBootstrapperRegistry The bootstrapper registry
     */
    public function read($fileName)
    {
        $registry = new BootstrapperRegistry($this->paths, $this->environment);

        if(file_exists($this->getCachedRegistryPath($fileName)))
        {
            return $this->loadRegistryFromCache($fileName, $registry);
        }
        else
        {
            // Write this for next time
            $registry = $this->loadRegistryFromBootstrapperClasses($registry);
            $this->write($fileName, $registry);

            return $registry;
        }
    }

    /**
     * Registers bootstrapper classes in the case that no cached registry was found
     * In this case, all the bootstrappers in this list are instantiated and later written to a cached registry
     *
     * @param array $bootstrapperClasses The list of bootstrapper classes
     */
    public function registerBootstrapperClasses(array $bootstrapperClasses)
    {
        $this->allBootstrappers = array_merge($this->allBootstrappers, $bootstrapperClasses);
    }

    /**
     * Writes the bootstrapper registry
     *
     * @param string $fileName The cache registry filename
     * @param IBootstrapperRegistry $registry The config to write
     */
    public function write($fileName, IBootstrapperRegistry $registry)
    {
        $data = [
            "eager" => $registry->getEagerBootstrapperClasses(),
            "lazy" => []
        ];

        foreach($registry->getBindingsToLazyBootstrapperClasses() as $boundClass => $bootstrapperClass)
        {
            $data["lazy"][$boundClass] = $bootstrapperClass;
        }

        file_put_contents($this->getCachedRegistryPath($fileName), json_encode($data));
    }

    /**
     * Loads a registry from the list of all bootstrappers
     *
     * @param IBootstrapperRegistry $registry The registry to load
     * @return IBootstrapperRegistry The registry
     */
    private function loadRegistryFromBootstrapperClasses(IBootstrapperRegistry $registry)
    {
        foreach($this->allBootstrappers as $bootstrapperClass)
        {
            $bootstrapper = $registry->getInstance($bootstrapperClass);

            if($bootstrapper instanceof ILazyBootstrapper)
            {
                $registry->registerLazyBootstrapper($bootstrapper->getBoundClasses(), $bootstrapperClass);
            }
            else
            {
                $registry->registerEagerBootstrapper($bootstrapperClass);
            }
        }

        return $registry;
    }

    /**
     * Loads a cached registry file's data into a registry
     *
     * @param string $fileName The cache registry file name
     * @param IBootstrapperRegistry $registry The registry to load
     * @return IBootstrapperRegistry The registry
     */
    private function loadRegistryFromCache($fileName, IBootstrapperRegistry $registry)
    {
        $rawContents = file_get_contents($this->getCachedRegistryPath($fileName));
        $decodedContents = json_decode($rawContents, true);

        foreach($decodedContents["eager"] as $eagerBootstrapperClass)
        {
            $registry->registerEagerBootstrapper($eagerBootstrapperClass);
        }

        foreach($decodedContents["lazy"] as $boundClass => $lazyBootstrapperClass)
        {
            $registry->registerLazyBootstrapper($boundClass, $lazyBootstrapperClass);
        }

        return $registry;
    }
}