<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for route compilers to implement
 */
namespace RDev\Routing\Compilers;
use RDev\HTTP;
use RDev\Routing\Routes;

interface ICompiler
{
    /**
     * Compiles a route
     *
     * @param Routes\Route $route The route to compile
     * @param HTTP\Request $request The request
     * @return Routes\CompiledRoute The compiled route
     */
    public function compile(Routes\Route $route, HTTP\Request $request);
}