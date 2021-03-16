<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Framework\Console\Commands;

use Opulence\Console\Commands\Command;
use Opulence\Console\Responses\IResponse;
use Opulence\Framework\Configuration\Config;
use Opulence\Ioc\Bootstrappers\Caching\ICache as BootstrapperCache;
use Opulence\Routing\Routes\Caching\ICache as RouteCache;
use Opulence\Views\Caching\ICache as ViewCache;

/**
 * Defines the command that flushes the framework's cache
 */
class FlushFrameworkCacheCommand extends Command
{
    /** @var BootstrapperCache The console kernel bootstrapper cache */
    private $consoleBootstrapperCache = null;
    /** @var BootstrapperCache The HTTP kernel bootstrapper cache */
    private $httpBootstrapperCache = null;
    /** @var RouteCache The route cache */
    private $routeCache = null;
    /** @var ViewCache The view cache */
    private $viewCache = null;

    /**
     * @param BootstrapperCache $httpBootstrapperCache The HTTP bootstrapper cache
     * @param BootstrapperCache $consoleBootstrapperCache The console bootstrapper cache
     * @param RouteCache $routeCache The route cache
     * @param ViewCache $viewCache The view cache
     */
    public function __construct(
        BootstrapperCache $httpBootstrapperCache,
        BootstrapperCache $consoleBootstrapperCache,
        RouteCache $routeCache,
        ViewCache $viewCache
    ) {
        parent::__construct();

        $this->httpBootstrapperCache = $httpBootstrapperCache;
        $this->consoleBootstrapperCache = $consoleBootstrapperCache;
        $this->routeCache = $routeCache;
        $this->viewCache = $viewCache;
    }

    /**
     * @inheritdoc
     */
    protected function define()
    {
        $this->setName('framework:flushcache')
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
        $response->writeln('<success>Framework cache flushed</success>');
    }

    /**
     * Flushes the bootstrapper cache
     *
     * @param IResponse $response The response to write to
     */
    private function flushBootstrapperCache(IResponse $response)
    {
        $this->httpBootstrapperCache->flush();
        $this->consoleBootstrapperCache->flush();

        $response->writeln('<info>Bootstrapper cache flushed</info>');
    }

    /**
     * Flushes the route cache
     *
     * @param IResponse $response The response to write to
     */
    private function flushRouteCache(IResponse $response)
    {
        if (($path = Config::get('paths', 'routes.cache')) !== null) {
            $this->routeCache->flush("$path/" . RouteCache::DEFAULT_CACHED_ROUTES_FILE_NAME);
        }

        $response->writeln('<info>Route cache flushed</info>');
    }

    /**
     * Flushes the view cache
     *
     * @param IResponse $response The response to write to
     */
    private function flushViewCache(IResponse $response)
    {
        $this->viewCache->flush();
        $response->writeln('<info>View cache flushed</info>');
    }
}
