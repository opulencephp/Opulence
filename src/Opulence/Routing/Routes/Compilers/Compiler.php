<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes\Compilers;

use Opulence\Http\Requests\Request;
use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Routing\Routes\Compilers\Matchers\HostMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\IRouteMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\PathMatcher;
use Opulence\Routing\Routes\ParsedRoute;

/**
 * Defines a route compiler
 */
class Compiler implements ICompiler
{
    /** @var IRouteMatcher[] The list of route matchers used by the compiler */
    private $routeMatchers = [];

    /**
     * @param IRouteMatcher[] The list of route matchers to use
     */
    public function __construct(array $routeMatchers)
    {
        $this->routeMatchers = $routeMatchers;
    }

    /**
     * @inheritdoc
     */
    public function compile(ParsedRoute $route, Request $request) : CompiledRoute
    {
        $hostMatches = [];
        $pathMatches = [];

        foreach ($this->routeMatchers as $routeMatcher) {
            if ($routeMatcher instanceof HostMatcher && !$routeMatcher->isMatch($route, $request, $hostMatches)) {
                return new CompiledRoute($route, false);
            } elseif ($routeMatcher instanceof PathMatcher && !$routeMatcher->isMatch($route, $request, $pathMatches)) {
                return new CompiledRoute($route, false);
            } elseif (!$routeMatcher->isMatch($route, $request)) {
                return new CompiledRoute($route, false);
            }
        }

        // If we've gotten here, then all the matchers matched
        $pathVars = array_merge($hostMatches, $pathMatches);

        return new CompiledRoute($route, true, $pathVars);
    }
}