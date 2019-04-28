<?php

/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2019 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */

declare(strict_types=1);

namespace Opulence\Routing\Middleware;

use Closure;
use Opulence\Http\Requests\Request;
use Opulence\Http\Responses\Response;

/**
 * Defines the interface for route middleware to implement
 */
interface IMiddleware
{
    /**
     * Handles a request
     *
     * @param Request $request The request to handle
     * @param Closure $next The next middleware item
     * @return Response The response after the middleware was run
     */
    public function handle(Request $request, Closure $next): Response;
}
