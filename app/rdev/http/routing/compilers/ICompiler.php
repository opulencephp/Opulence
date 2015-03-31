<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for route compilers to implement
 */
namespace RDev\HTTP\Routing\Compilers;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Routing\Routes\CompiledRoute;
use RDev\HTTP\Routing\Routes\Route;

interface ICompiler
{
    /**
     * Compiles a route
     *
     * @param Route $route The route to compile
     * @param Request $request The request
     * @return CompiledRoute The compiled route
     */
    public function compile(Route $route, Request $request);
}