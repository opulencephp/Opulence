<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the bootstrapper reader/writer
 */
namespace RDev\Applications\Bootstrappers\IO;
use RDev\Applications\Bootstrappers\IBootstrapperRegistry;
use RDev\Applications\Paths;

class BootstrapperIO implements IBootstrapperIO
{
    /** @var Paths The application paths */
    private $paths = null;

    /**
     * @param Paths $paths The application paths
     */
    public function __construct(Paths $paths)
    {
        $this->paths = $paths;
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
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
    protected function loadRegistryFromCache($filePath, IBootstrapperRegistry &$registry)
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