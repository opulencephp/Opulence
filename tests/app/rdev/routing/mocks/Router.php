<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks the router for use in testing
 */
namespace RDev\Tests\Routing\Mocks;
use RDev\IoC\Container;
use RDev\Routing\Router as BaseRouter;
use RDev\Routing\Routes\Compilers\Compiler;
use RDev\Routing\Routes\Compilers\Matchers\HostMatcher;
use RDev\Routing\Routes\Compilers\Matchers\PathMatcher;
use RDev\Routing\Routes\Compilers\Matchers\SchemeMatcher;
use RDev\Routing\Routes\Compilers\Parsers\Parser;
use RDev\Tests\Routing\Dispatchers\Mocks\Dispatcher;

class Router extends BaseRouter
{
    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        $routeMatchers = [
            new SchemeMatcher(),
            new HostMatcher(),
            new PathMatcher()
        ];
        $parser = new Parser();
        $compiler = new Compiler($routeMatchers);

        parent::__construct(new Dispatcher(new Container()), $compiler, $parser);
    }
} 