<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2017 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Routing\Routes\Compilers\Matchers;

use Opulence\Http\Requests\Request;
use Opulence\Routing\Routes\ParsedRoute;

/**
 * Defines the host matcher
 */
class HostMatcher implements IRouteMatcher
{
    /**
     * @inheritdoc
     * @param array $matches The list of regex matches
     */
    public function isMatch(ParsedRoute $route, Request $request, array &$matches = []) : bool
    {
        $isMatch = preg_match($route->getHostRegex(), $request->getHeaders()->get("HOST"), $matches) === 1;
        // Remove the subject
        array_shift($matches);

        return $isMatch;
    }
}
