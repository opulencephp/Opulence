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
 * Defines the path matcher
 */
class PathMatcher implements IRouteMatcher
{
    /**
     * @inheritdoc
     * @param array $matches The list of regex matches
     */
    public function isMatch(ParsedRoute $route, Request $request, array &$matches = [])
    {
        $isMatch = preg_match($route->getPathRegex(), $request->getPath(), $matches) === 1;
        // Remove the subject
        array_shift($matches);

        return $isMatch;
    }
}