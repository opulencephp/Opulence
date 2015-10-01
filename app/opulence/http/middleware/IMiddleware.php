<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Defines the interface for HTTP middleware to implement
 */
namespace Opulence\HTTP\Middleware;

use Closure;
use Opulence\HTTP\Requests\Request;
use Opulence\HTTP\Responses\Response;

interface IMiddleware
{
    /**
     * Handles a request
     *
     * @param Request $request The request to handle
     * @param Closure $next The next middleware item
     * @return Response The response after the middleware was run
     */
    public function handle(Request $request, Closure $next);
}