<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for route parsers to implement
 */
namespace Opulence\Routing\Routes\Compilers\Parsers;

use Opulence\Routing\Routes\Route;
use Opulence\Routing\RouteException;
use Opulence\Routing\Routes\ParsedRoute;

interface IParser
{
    /**
     * Parses a route into regular expressions
     *
     * @param Route $route The route to parse
     * @return ParsedRoute The parsed route
     * @throws RouteException Thrown if the route is not valid
     */
    public function parse(Route $route);
} 