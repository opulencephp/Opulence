<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for route compilers to implement
 */
namespace RDev\HTTP\Routing\Compilers;
use RDev\HTTP\Requests;
use RDev\HTTP\Routing\Routes;

interface ICompiler
{
    /**
     * Compiles a route
     *
     * @param Routes\Route $route The route to compile
     * @param Requests\Request $request The request
     * @return Routes\CompiledRoute The compiled route
     */
    public function compile(Routes\Route $route, Requests\Request $request);
}