<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Routing\Tests\Routes\Caching;

use Opulence\Routing\Dispatchers\MiddlewarePipeline;
use Opulence\Routing\Dispatchers\RouteDispatcher;
use Opulence\Routing\Router;
use Opulence\Routing\Routes\Caching\FileCache;
use Opulence\Routing\Routes\Compilers\Compiler;
use Opulence\Routing\Routes\Compilers\Parsers\Parser;
use Opulence\Routing\Tests\Dispatchers\Mocks\DependencyResolver;

/**
 * Tests the route cache
 */
class FileCacheTest extends \PHPUnit\Framework\TestCase
{
    /** @var FileCache The cache to use in tests */
    private $cache = null;
    /** @var string The path to the cache file */
    private $cachedRouteFilePath = '';
    /** @var string The path to the raw file */
    private $rawRouteFilePath = '';

    /**
     * Sets up the tests
     */
    public function setUp() : void
    {
        $this->cache = new FileCache();
        $this->cachedRouteFilePath = __DIR__ . '/files/' . FileCache::DEFAULT_CACHED_ROUTES_FILE_NAME;
        $this->rawRouteFilePath = __DIR__ . '/files/raw.php';
    }

    /**
     * Tears down the tests
     */
    public function tearDown() : void
    {
        if (file_exists($this->cachedRouteFilePath)) {
            @unlink($this->cachedRouteFilePath);
        }
    }

    /**
     * Tests flushing the cache
     */
    public function testFlushing()
    {
        file_put_contents($this->cachedRouteFilePath, 'foo');
        $this->cache->flush($this->cachedRouteFilePath);
        $this->assertFileDoesNotExist($this->cachedRouteFilePath);
    }

    /**
     * Tests that the routes are cached after a miss
     */
    public function testRoutesAreCachedAfterMiss()
    {
        $router = $this->getRouter();
        $this->assertFileDoesNotExist($this->cachedRouteFilePath);
        $routes = $this->cache->get($this->cachedRouteFilePath, $router, $this->rawRouteFilePath);
        $this->assertFileExists($this->cachedRouteFilePath);
        $this->assertEquals(base64_encode(serialize($routes)), file_get_contents($this->cachedRouteFilePath));
    }

    /**
     * Tests that the routes are read from cache
     */
    public function testRoutesAreReadFromCache()
    {
        $router = $this->getRouter();
        require $this->rawRouteFilePath;
        $routes = $router->getRouteCollection();
        file_put_contents($this->cachedRouteFilePath, base64_encode(serialize($routes)));
        $routes = $this->cache->get($this->cachedRouteFilePath, $router, $this->rawRouteFilePath);
        $this->assertFileExists($this->cachedRouteFilePath);
        $this->assertEquals(base64_encode(serialize($routes)), file_get_contents($this->cachedRouteFilePath));
    }

    /**
     * Tests setting and then getting from cache
     */
    public function testSettingAndGettingFromCache()
    {
        $router = $this->getRouter();
        require $this->rawRouteFilePath;
        $setRoutes = $router->getRouteCollection();
        $this->cache->set($this->cachedRouteFilePath, $setRoutes);
        $this->assertEquals(base64_encode(serialize($setRoutes)), file_get_contents($this->cachedRouteFilePath));
        $this->assertFileExists($this->cachedRouteFilePath);
        $getRoutes = $this->cache->get($this->cachedRouteFilePath, $router, $this->rawRouteFilePath);
        $this->assertEquals($getRoutes, $setRoutes);
    }

    /**
     * Gets a router instance to use in tests
     *
     * @return Router The router to use
     */
    private function getRouter()
    {
        return new Router(
            new RouteDispatcher(new DependencyResolver(), new MiddlewarePipeline()),
            new Compiler([]),
            new Parser()
        );
    }
}
