<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Framework\Http\Console\Commands;

use Opulence\Bootstrappers\Caching\ICache as BootstrapperCache;
use Opulence\Bootstrappers\Paths;
use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;
use Opulence\Routing\Routes\Caching\ICache as RouteCache;
use Opulence\Views\Caching\ICache as ViewCache;

/**
 * Defines the command that flushes the framework's cache
 */
class FlushFrameworkCacheCommand extends Command
{
    /** @var Paths The application paths */
    private $paths = null;
    /** @var BootstrapperCache The bootstrapper cache */
    private $bootstrapperCache = null;
    /** @var RouteCache The route cache */
    private $routeCache = null;
    /** @var ViewCache The view cache */
    private $viewCache = null;

    /**
     * @param Paths $paths The application paths
     * @param BootstrapperCache $bootstrapperCache The bootstrapper cache
     * @param RouteCache $routeCache The route cache
     * @param ViewCache $viewCache The view cache
     */
    public function __construct(
        Paths $paths,
        BootstrapperCache $bootstrapperCache,
        RouteCache $routeCache,
        ViewCache $viewCache
    ) {
        parent::__construct();

        $this->paths = $paths;
        $this->bootstrapperCache = $bootstrapperCache;
        $this->routeCache = $routeCache;
        $this->viewCache = $viewCache;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName("framework:flushcache")
            ->setDescription("Flushes all of the framework's cached files");
    }

    /**
     * @inheritdoc
     */
    protected function doExecute(IResponse $response)
    {
        $this->flushBootstrapperCache($response);
        $this->flushRouteCache($response);
        $this->flushViewCache($response);
        $response->writeln("<success>Framework cache flushed</success>");
    }

    /**
     * Flushes the bootstrapper cache
     *
     * @param IResponse $response The response to write to
     */
    private function flushBootstrapperCache(IResponse $response)
    {
        $fileNames = [];

        if (isset($this->paths["tmp.framework.console"])) {
            $fileNames[] = "{$this->paths["tmp.framework.console"]}/" . BootstrapperCache::DEFAULT_CACHED_REGISTRY_FILE_NAME;
        }

        if (isset($this->paths["tmp.framework.http"])) {
            $fileNames[] = "{$this->paths["tmp.framework.http"]}/" . BootstrapperCache::DEFAULT_CACHED_REGISTRY_FILE_NAME;
        }

        foreach ($fileNames as $cachedRegistryFileName) {
            $this->bootstrapperCache->flush($cachedRegistryFileName);
        }

        $response->writeln("<info>Bootstrapper cache flushed</info>");
    }

    /**
     * Flushes the route cache
     *
     * @param IResponse $response The response to write to
     */
    private function flushRouteCache(IResponse $response)
    {
        if (isset($this->paths["routes.cache"])) {
            $this->routeCache->flush("{$this->paths["routes.cache"]}/" . RouteCache::DEFAULT_CACHED_ROUTES_FILE_NAME);
        }

        $response->writeln("<info>Route cache flushed</info>");
    }

    /**
     * Flushes the view cache
     *
     * @param IResponse $response The response to write to
     */
    private function flushViewCache(IResponse $response)
    {
        $this->viewCache->flush();
        $response->writeln("<info>View cache flushed</info>");
    }
}