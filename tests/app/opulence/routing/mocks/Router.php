<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the router for use in testing
 */
namespace Opulence\Tests\Routing\Mocks;
use Opulence\IoC\Container;
use Opulence\Routing\Router as BaseRouter;
use Opulence\Routing\Routes\Compilers\Compiler;
use Opulence\Routing\Routes\Compilers\Matchers\HostMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\PathMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\SchemeMatcher;
use Opulence\Routing\Routes\Compilers\Parsers\Parser;
use Opulence\Tests\Routing\Dispatchers\Mocks\Dispatcher;

class Router extends BaseRouter
{
    /**
     * {@inheritdoc}
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

        parent::__construct(new Dispatcher(new Container()), $compiler, $parser);
    }
} 