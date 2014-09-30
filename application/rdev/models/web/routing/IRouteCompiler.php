<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for route compilers to implement
 */
namespace RDev\Models\Web\Routing;

interface IRouteCompiler
{
    /**
     * Compiles a route into regular expressions
     *
     * @param Route $route The route to compile
     */
    public function compile(Route &$route);
} 