<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the bootstrapper reader/writer
 */
namespace RDev\Applications\Bootstrappers;
use RDev\Applications\Paths;

class BootstrapperIO
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
     * Reads the bootstrapper registry
     *
     * @return BootstrapperRegistry|null The bootstrapper registry if there was one, otherwise null
     */
    public function read()
    {

    }

    /**
     * Writes the bootstrapper registry
     *
     * @param BootstrapperRegistry $registry The config to write
     */
    public function write(BootstrapperRegistry $registry)
    {

    }
}