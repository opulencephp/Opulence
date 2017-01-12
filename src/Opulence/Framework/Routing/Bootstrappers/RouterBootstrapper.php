<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

namespace Opulence\Framework\Routing\Bootstrappers;

use Opulence\Ioc\Bootstrappers\Bootstrapper;
use Opulence\Ioc\IContainer;
use Opulence\Routing\Dispatchers\ContainerDependencyResolver;
use Opulence\Routing\Dispatchers\IRouteDispatcher;
use Opulence\Routing\Dispatchers\MiddlewarePipeline;
use Opulence\Routing\Dispatchers\RouteDispatcher;
use Opulence\Routing\Router;
use Opulence\Routing\Routes\Caching\FileCache;
use Opulence\Routing\Routes\Caching\ICache;
use Opulence\Routing\Routes\Compilers\Compiler;
use Opulence\Routing\Routes\Compilers\ICompiler;
use Opulence\Routing\Routes\Compilers\Matchers\HostMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\IRouteMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\PathMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\SchemeMatcher;
use Opulence\Routing\Routes\Compilers\Parsers\IParser;
use Opulence\Routing\Routes\Compilers\Parsers\Parser;
use Opulence\Routing\Urls\UrlGenerator;

/**
 * Defines the routing bootstrapper
 */
class RouterBootstrapper extends Bootstrapper
{
    /** @var ICache The route cache */
    protected $cache = null;
    /** @var IParser The route parser */
    protected $parser = null;

    /**
     * @inheritdoc
     */
    public function registerBindings(IContainer $container)
    {
        $this->cache = $this->getRouteCache($container);
        $dispatcher = $this->getRouteDispatcher($container);
        $this->parser = $this->getRouteParser($container);
        $compiler = $this->getRouteCompiler($container);
        $router = new Router($dispatcher, $compiler, $this->parser);
        $this->configureRouter($router);
        $urlGenerator = new UrlGenerator($router->getRouteCollection());
        $container->bindInstance(ICache::class, $this->cache);
        $container->bindInstance(IRouteDispatcher::class, $dispatcher);
        $container->bindInstance(ICompiler::class, $compiler);
        $container->bindInstance(Router::class, $router);
        $container->bindInstance(UrlGenerator::class, $urlGenerator);
    }

    /**
     * Configures the router, which is useful for things like caching
     *
     * @param Router $router The router to configure
     */
    protected function configureRouter(Router $router)
    {
        // Let extending classes define this
    }

    /**
     * Gets the route cache
     * To use a different route cache than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return ICache The route cache
     */
    protected function getRouteCache(IContainer $container) : ICache
    {
        return new FileCache();
    }

    /**
     * Gets the route compiler
     * To use a different route compiler than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return ICompiler The route compiler
     */
    protected function getRouteCompiler(IContainer $container) : ICompiler
    {
        return new Compiler($this->getRouteMatchers($container));
    }

    /**
     * Gets the route dispatcher
     * To use a different route dispatcher than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return IRouteDispatcher The route dispatcher
     */
    protected function getRouteDispatcher(IContainer $container) : IRouteDispatcher
    {
        return new RouteDispatcher(new ContainerDependencyResolver($container), new MiddlewarePipeline());
    }

    /**
     * Gets the list of route matchers
     *
     * @param IContainer $container The dependency injection container
     * @return IRouteMatcher[] The list of route matchers
     */
    protected function getRouteMatchers(IContainer $container) : array
    {
        return [
            new PathMatcher(),
            new HostMatcher(),
            new SchemeMatcher()
        ];
    }

    /**
     * Gets the route parser
     * To use a different route parser than the one returned here, extend this class and override this method
     *
     * @param IContainer $container The dependency injection container
     * @return IParser The route parser
     */
    protected function getRouteParser(IContainer $container) : IParser
    {
        return new Parser();
    }
}
