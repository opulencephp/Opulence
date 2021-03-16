<?php

/*
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2021 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/1.2/LICENSE.md
 */

namespace Opulence\Routing\Tests\Mocks;

use Opulence\Routing\Dispatchers\MiddlewarePipeline;
use Opulence\Routing\Router as BaseRouter;
use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Routing\Routes\Compilers\Compiler;
use Opulence\Routing\Routes\Compilers\Matchers\HostMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\PathMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\SchemeMatcher;
use Opulence\Routing\Routes\Compilers\Parsers\Parser;
use Opulence\Routing\Tests\Dispatchers\Mocks\DependencyResolver;
use Opulence\Routing\Tests\Dispatchers\Mocks\RouteDispatcher;

/**
 * Mocks the router for use in testing
 */
class Router extends BaseRouter
{
    /** @var RouteDispatcher The mock dispatcher */
    protected $dispatcher = null;

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $routeMatchers = [
            new PathMatcher(),
            new HostMatcher(),
            new SchemeMatcher()
        ];
        $parser = new Parser();
        $compiler = new Compiler($routeMatchers);

        parent::__construct(
            new RouteDispatcher(new DependencyResolver(), new MiddlewarePipeline()),
            $compiler,
            $parser
        );
    }

    /**
     * Gets the last route dispatched
     *
     * @return CompiledRoute The last route
     */
    public function getLastRoute() : CompiledRoute
    {
        return $this->dispatcher->getLastRoute();
    }
}
