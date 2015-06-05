<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for bootstrapper IO to implement
 */
namespace RDev\Applications\Bootstrappers\IO;
use RDev\Applications\Bootstrappers\IBootstrapperRegistry;

interface IBootstrapperIO
{
    /** The default cached registry file name */
    const DEFAULT_CACHED_REGISTRY_FILE_NAME = "cachedBootstrapperRegistry.json";

    /**
     * Reads the bootstrapper details from cache, if it exists, otherwise manually sets the details and caches them
     *
     * @param string $filePath The cache registry file path
     * @param IBootstrapperRegistry $registry The registry to read settings into
     */
    public function read($filePath, IBootstrapperRegistry &$registry);

    /**
     * Writes the bootstrapper registry
     *
     * @param string $filePath The cache registry file path
     * @param IBootstrapperRegistry $registry The config to write
     */
    public function write($filePath, IBootstrapperRegistry $registry);
}