<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the scheme matcher
 */
namespace Opulence\Routing\Routes\Compilers\Matchers;

use Opulence\HTTP\Requests\Request;
use Opulence\Routing\Routes\ParsedRoute;

class SchemeMatcher implements IRouteMatcher
{
    /**
     * @inheritdoc
     */
    public function isMatch(ParsedRoute $route, Request $request)
    {
        return ($route->isSecure() && $request->isSecure()) || !$route->isSecure();
    }
}