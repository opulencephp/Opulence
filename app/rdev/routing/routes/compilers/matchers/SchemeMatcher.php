<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the scheme matcher
 */
namespace RDev\Routing\Routes\Compilers\Matchers;
use RDev\HTTP\Requests\Request;
use RDev\Routing\Routes\ParsedRoute;

class SchemeMatcher implements IRouteMatcher
{
    /**
     * {@inheritdoc}
     */
    public function isMatch(ParsedRoute $route, Request $request)
    {
        return ($route->isSecure() && $request->isSecure()) || !$route->isSecure();
    }
}