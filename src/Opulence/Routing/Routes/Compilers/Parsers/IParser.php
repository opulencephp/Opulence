<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Routes\Compilers\Parsers;

use Opulence\Routing\RouteException;
use Opulence\Routing\Routes\ParsedRoute;
use Opulence\Routing\Routes\Route;

/**
 * Defines the interface for route parsers to implement
 */
interface IParser
{
    /**
     * Parses a route into regular expressions
     *
     * @param Route $route The route to parse
     * @return ParsedRoute The parsed route
     * @throws RouteException Thrown if the route is not valid
     */
    public function parse(Route $route): ParsedRoute;
}
