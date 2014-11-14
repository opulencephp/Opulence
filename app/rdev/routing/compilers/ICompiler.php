<?php
/**
 * Copyright (C) 2014 David Young
 *
 * Defines the interface for route compilers to implement
 */
namespace RDev\Routing\Compilers;
use RDev\Routing;

interface ICompiler
{
    /**
     * Compiles a route into regular expressions
     *
     * @param Routing\Route $route The route to compile
     * @throws Routing\RouteException Thrown if the route is not valid
     */
    public function compile(Routing\Route &$route);

    /**
     * Gets the regex that matches variables that appear in a route
     *
     * @return string The regex that matches variables in the route
     */
    public function getVariableMatchingRegex();
} 