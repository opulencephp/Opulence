<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for route compilers to implement
 */
namespace Opulence\Routing\Routes\Compilers;

use Opulence\HTTP\Requests\Request;
use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Routing\Routes\ParsedRoute;

interface ICompiler
{
    /**
     * Compiles a route
     *
     * @param ParsedRoute $route The route to compile
     * @param Request $request The request
     * @return CompiledRoute The compiled route
     */
    public function compile(ParsedRoute $route, Request $request);
}