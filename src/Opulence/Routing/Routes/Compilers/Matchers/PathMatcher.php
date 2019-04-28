<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

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
    public function isMatch(ParsedRoute $route, Request $request, array &$matches = []): bool
    {
        $isMatch = preg_match($route->getPathRegex(), $request->getPath(), $matches) === 1;
        // Remove the subject
        array_shift($matches);

        return $isMatch;
    }
}
