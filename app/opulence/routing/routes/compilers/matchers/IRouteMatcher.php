<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2015 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes\Compilers\Matchers;

use Opulence\Http\Requests\Request;
use Opulence\Routing\Routes\ParsedRoute;

/**
 * Defines the interface for route matchers to implement
 */
interface IRouteMatcher
{
    /**
     * Gets whether or not a route is a match for the current request
     *
     * @param ParsedRoute $route The current route
     * @param Request $request The current request
     * @return bool True if the route is a match, otherwise false
     */
    public function isMatch(ParsedRoute $route, Request $request);
}