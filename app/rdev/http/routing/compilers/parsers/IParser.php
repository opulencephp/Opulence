<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for route parsers to implement
 */
namespace RDev\HTTP\Routing\Compilers\Parsers;
use RDev\HTTP\Routing\Routes\Route;
use RDev\HTTP\Routing\RouteException;
use RDev\HTTP\Routing\Routes\ParsedRoute;

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
     * @param Route $route The route to parse
     * @return ParsedRoute The parsed route
     * @throws RouteException Thrown if the route is not valid
     */
    public function parse(Route $route);
} 