<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Ioc\Bootstrappers\Caching;

use Opulence\Ioc\Bootstrappers\IBootstrapperRegistry;

/**
 * Defines the interface for bootstrapper caches to implement
 */
interface ICache
{
    /** The default cached registry file name */
    const DEFAULT_CACHED_REGISTRY_FILE_NAME = "cachedBootstrapperRegistry.json";

    /**
     * Flushes the cache
     *
     * @param string $filePath The cache registry file path
     */
    public function flush(string $filePath);

    /**
     * Reads the bootstrapper details from cache, if it exists, otherwise manually sets the details and caches them
     *
     * @param string $filePath The cache registry file path
     * @param IBootstrapperRegistry $registry The registry to read settings into
     */
    public function get(string $filePath, IBootstrapperRegistry &$registry);

    /**
     * Writes the bootstrapper registry
     *
     * @param string $filePath The cache registry file path
     * @param IBootstrapperRegistry $registry The config to write
     */
    public function set(string $filePath, IBootstrapperRegistry $registry);
}