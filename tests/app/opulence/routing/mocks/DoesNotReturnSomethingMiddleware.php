<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks middleware that does not return something
 */
namespace Opulence\Tests\Routing\Mocks;
use Closure;
use Opulence\HTTP\Middleware\IMiddleware;
use Opulence\HTTP\Requests\Request;

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