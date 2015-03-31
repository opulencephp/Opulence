<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for HTTP middleware to implement
 */
namespace RDev\HTTP\Middleware;
use Closure;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\Response;

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