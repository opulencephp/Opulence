<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines a route compiler
 */
namespace Opulence\Routing\Routes\Compilers;
use Opulence\HTTP\Requests\Request;
use Opulence\Routing\Routes\Compilers\Matchers\HostMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\IRouteMatcher;
use Opulence\Routing\Routes\Compilers\Matchers\PathMatcher;
use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Routing\Routes\ParsedRoute;

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
     * {@inheritdoc}
     */
    public function compile(ParsedRoute $route, Request $request)
    {
        $hostMatches = [];
        $pathMatches = [];

        foreach($this->routeMatchers as $routeMatcher)
        {
            if($routeMatcher instanceof HostMatcher && !$routeMatcher->isMatch($route, $request, $hostMatches))
            {
                return new CompiledRoute($route, false);
            }
            elseif($routeMatcher instanceof PathMatcher && !$routeMatcher->isMatch($route, $request, $pathMatches))
            {
                return new CompiledRoute($route, false);
            }
            elseif(!$routeMatcher->isMatch($route, $request))
            {
                return new CompiledRoute($route, false);
            }
        }

        // If we've gotten here, then all the matchers matched
        $pathVariables = array_merge($hostMatches, $pathMatches);

        return new CompiledRoute($route, true, $pathVariables);
    }
}