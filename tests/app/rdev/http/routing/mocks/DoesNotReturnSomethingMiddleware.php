<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks middleware that does not return something
 */
namespace RDev\Tests\HTTP\Routing\Mocks;
use RDev\HTTP\Middleware;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing\Routes;

class DoesNotReturnSomethingMiddleware implements Middleware\IMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function handle(Requests\Request $request, \Closure $next)
    {
        return $next($request);
    }
}