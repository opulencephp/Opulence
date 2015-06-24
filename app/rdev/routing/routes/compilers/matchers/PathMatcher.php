<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the path matcher
 */
namespace RDev\Routing\Routes\Compilers\Matchers;
use RDev\HTTP\Requests\Request;
use RDev\Routing\Routes\ParsedRoute;

class PathMatcher implements IRouteMatcher
{
    /**
     * {@inheritdoc}
     * @param array $matches The list of regex matches
     */
    public function isMatch(ParsedRoute $route, Request $request, array &$matches = [])
    {
        return preg_match($route->getPathRegex(), $request->getPath(), $matches) === 1;
    }
}