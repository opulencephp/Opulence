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
    /** The name of the cached registry file */
    const CACHED_REGISTRY_FILE_NAME = "cachedRegistry.json";

    /** @var Paths The application paths */
    private $paths = null;
    /** @var Environment The current environment */
    private $environment = null;
    /** @var array The list of all bootstrapper classes in the application */
    private $allBootstrappers = [];

    /**
     * @param Paths $paths The application paths
     * @param Environment $environment The current environment
     */
    public function __construct(Paths $paths, Environment $environment)
    {
        $this->paths = $paths;
        $this->environment = $environment;
    }

    /**
     * Reads the bootstrapper registry from cache, if it exists, otherwise it generates a registry from the list of
     * bootstrappers
     *
     * @return IBootstrapperRegistry The bootstrapper registry
     */
    public function read()
    {
        $registry = new BootstrapperRegistry($this->paths, $this->environment);

        if(file_exists($this->getCachedRegistryFileName()))
        {
            return $this->loadRegistryFromCache($registry);
        }
        else
        {
            return $this->loadRegistryFromBootstrapperClasses($registry);
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
     * @param IBootstrapperRegistry $registry The config to write
     */
    public function write(IBootstrapperRegistry $registry)
    {
        $data = [
            "eager" => $registry->getEagerBootstrapperClasses(),
            "lazy" => []
        ];

        foreach($registry->getBindingsToLazyBootstrapperClasses() as $boundClass => $bootstrapperClass)
        {
            $data["lazy"][$boundClass] = $bootstrapperClass;
        }

        file_put_contents($this->getCachedRegistryFileName(), json_encode($data));
    }

    /**
     * Gets the cached registry file name
     *
     * @return string The cached registry file name
     */
    private function getCachedRegistryFileName()
    {
        return "{$this->paths["tmp.framework"]}/" . self::CACHED_REGISTRY_FILE_NAME;
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
     * @param IBootstrapperRegistry $registry The registry to load
     * @return IBootstrapperRegistry The registry
     */
    private function loadRegistryFromCache(IBootstrapperRegistry $registry)
    {
        $rawContents = file_get_contents($this->getCachedRegistryFileName());
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