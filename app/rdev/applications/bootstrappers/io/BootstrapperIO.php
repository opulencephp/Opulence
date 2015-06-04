<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the bootstrapper reader/writer
 */
namespace RDev\Applications\Bootstrappers\IO;
use RDev\Applications\Bootstrappers\IBootstrapperRegistry;
use RDev\Applications\Environments\Environment;
use RDev\Applications\Paths;

class BootstrapperIO
{
    /** The default cached registry file name */
    const DEFAULT_CACHED_REGISTRY_FILE_NAME = "cachedBootstrapperRegistry.json";

    /** @var Paths The application paths */
    private $paths = null;
    /** @var Environment The current environment */
    private $environment = null;

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
     * Reads the bootstrapper details from cache, if it exists, otherwise manually sets the details and caches them
     *
     * @param string $filePath The cache registry file path
     * @param IBootstrapperRegistry $registry The registry to read settings into
     */
    public function read($filePath, IBootstrapperRegistry &$registry)
    {
        if(file_exists($filePath))
        {
            $this->loadRegistryFromCache($filePath, $registry);
        }
        else
        {
            $registry->setBootstrapperDetails();
            // Write this for next time
            $this->write($filePath, $registry);
        }
    }

    /**
     * Writes the bootstrapper registry
     *
     * @param string $filePath The cache registry file path
     * @param IBootstrapperRegistry $registry The config to write
     */
    public function write($filePath, IBootstrapperRegistry $registry)
    {
        $data = [
            "eager" => $registry->getEagerBootstrapperClasses(),
            "lazy" => []
        ];

        foreach($registry->getBindingsToLazyBootstrapperClasses() as $boundClass => $bootstrapperClass)
        {
            $data["lazy"][$boundClass] = $bootstrapperClass;
        }

        file_put_contents($filePath, json_encode($data));
    }

    /**
     * Loads a cached registry file's data into a registry
     *
     * @param string $filePath The cache registry file path
     * @param IBootstrapperRegistry $registry The registry to read settings into
     */
    private function loadRegistryFromCache($filePath, IBootstrapperRegistry &$registry)
    {
        $rawContents = file_get_contents($filePath);
        $decodedContents = json_decode($rawContents, true);

        foreach($decodedContents["eager"] as $eagerBootstrapperClass)
        {
            $registry->registerEagerBootstrapper($eagerBootstrapperClass);
        }

        foreach($decodedContents["lazy"] as $boundClass => $lazyBootstrapperClass)
        {
            $registry->registerLazyBootstrapper($boundClass, $lazyBootstrapperClass);
        }
    }
}