<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for route parsers to implement
 */
namespace RDev\Routing\Compilers\Parsers;
use RDev\Routing;
use RDev\Routing\Routes;

interface IParser
{
    /**
     * Gets the regex that matches variables that appear in a route
     *
     * @return string The regex that matches variables in the route
     */
    public function getVariableMatchingRegex();

    /**
     * Parses a route into regular expressions
     *
     * @param Routes\Route $route The route to parse
     * @return Routes\ParsedRoute The parsed route
     * @throws Routing\RouteException Thrown if the route is not valid
     */
    public function parse(Routes\Route $route);
} 