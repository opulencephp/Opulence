<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks middleware that does not return something
 */
namespace RDev\Tests\HTTP\Routing\Mocks;
use Closure;
use RDev\HTTP\Middleware\IMiddleware;
use RDev\HTTP\Requests\Request;

class DoesNotReturnSomethingMiddleware implements IMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }
}