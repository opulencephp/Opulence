<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for route compilers to implement
 */
namespace RDev\Routing;

interface IRouteCompiler
{
    /**
     * Compiles a route into regular expressions
     *
     * @param Route $route The route to compile
     * @throws RouteException Thrown if the route is not valid
     */
    public function compile(Route &$route);
} 