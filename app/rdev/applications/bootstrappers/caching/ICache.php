<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for bootstrapper caches to implement
 */
namespace RDev\Applications\Bootstrappers\Caching;
use RDev\Applications\Bootstrappers\IBootstrapperRegistry;

interface ICache
{
    /** The default cached registry file name */
    const DEFAULT_CACHED_REGISTRY_FILE_NAME = "cachedBootstrapperRegistry.json";

    /**
     * Flushes the cache
     *
     * @param string $filePath The cache registry file path
     */
    public function flush($filePath);

    /**
     * Reads the bootstrapper details from cache, if it exists, otherwise manually sets the details and caches them
     *
     * @param string $filePath The cache registry file path
     * @param IBootstrapperRegistry $registry The registry to read settings into
     */
    public function get($filePath, IBootstrapperRegistry &$registry);

    /**
     * Writes the bootstrapper registry
     *
     * @param string $filePath The cache registry file path
     * @param IBootstrapperRegistry $registry The config to write
     */
    public function set($filePath, IBootstrapperRegistry $registry);
}