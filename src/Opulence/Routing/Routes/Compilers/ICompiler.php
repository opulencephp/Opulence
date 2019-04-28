<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Routes\Compilers;

use Opulence\Http\Requests\Request;
use Opulence\Routing\Routes\CompiledRoute;
use Opulence\Routing\Routes\ParsedRoute;

/**
 * Defines the interface for route compilers to implement
 */
interface ICompiler
{
    /**
     * Compiles a route
     *
     * @param ParsedRoute $route The route to compile
     * @param Request $request The request
     * @return CompiledRoute The compiled route
     */
    public function compile(ParsedRoute $route, Request $request): CompiledRoute;
}
