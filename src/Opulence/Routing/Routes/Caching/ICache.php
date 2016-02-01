<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes\Caching;

use Opulence\Routing\Router;
use Opulence\Routing\Routes\RouteCollection;

/**
 * Defines the interface for route caches to implement
 */
interface ICache
{
    /** The default name of the cached routes file */
    const DEFAULT_CACHED_ROUTES_FILE_NAME = "cachedRoutes.php";

    /**
     * Flushes the cache
     *
     * @param string $filePath The path where the route collection resides
     */
    public function flush(string $filePath);

    /**
     * Gets the route collection from cache
     *
     * @param string $cacheFilePath The path where the cached route collection resides
     * @param Router $router The router to use in case the raw file has to be loaded
     * @param string $rawFilePath The path to the raw route collection in case of a cache miss
     * @return RouteCollection The route collection
     */
    public function get(string $cacheFilePath, Router &$router, string $rawFilePath) : RouteCollection;

    /**
     * Sets the route collection in cache
     *
     * @param string $filePath The path where the route collection resides
     * @param RouteCollection $routes The route collection to store
     */
    public function set(string $filePath, RouteCollection $routes);
}