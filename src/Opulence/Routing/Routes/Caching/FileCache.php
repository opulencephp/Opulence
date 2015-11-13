<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes\Caching;

use Opulence\Routing\Router;
use Opulence\Routing\Routes\RouteCollection;

/**
 * Defines the route file cache
 */
class FileCache implements ICache
{
    /**
     * @inheritdoc
     */
    public function flush($filePath)
    {
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    /**
     * @inheritdoc
     */
    public function get($cacheFilePath, Router &$router, $rawFilePath)
    {
        if (file_exists($cacheFilePath)) {
            return unserialize(base64_decode(file_get_contents($cacheFilePath)));
        } else {
            // The raw file should have a router variable named "$router", which corresponds with the router parameter
            require $rawFilePath;
            $routes = $router->getRouteCollection();
            $this->set($cacheFilePath, $routes);

            return $routes;
        }
    }

    /**
     * @inheritdoc
     */
    public function set($filePath, RouteCollection $routes)
    {
        // Clone the routes so that serialization of closures can work correctly
        file_put_contents($filePath, base64_encode(serialize(clone $routes)));
    }
}