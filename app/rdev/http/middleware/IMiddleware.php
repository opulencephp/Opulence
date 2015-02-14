<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Defines the interface for HTTP middleware to implement
 */
namespace RDev\HTTP\Middleware;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;

interface IMiddleware
{
    /**
     * Handles a request
     *
     * @param Requests\Request $request The request to handle
     * @param \Closure $next The next middleware item
     * @return Responses\Response The response after the middleware was run
     */
    public function handle(Requests\Request $request, \Closure $next);
}