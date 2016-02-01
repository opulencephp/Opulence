<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes\Compilers\Matchers;

use Opulence\Http\Requests\Request;
use Opulence\Routing\Routes\ParsedRoute;

/**
 * Defines the scheme matcher
 */
class SchemeMatcher implements IRouteMatcher
{
    /**
     * @inheritdoc
     */
    public function isMatch(ParsedRoute $route, Request $request) : bool
    {
        return ($route->isSecure() && $request->isSecure()) || !$route->isSecure();
    }
}