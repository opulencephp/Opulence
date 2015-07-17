<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks a middleware that writes to the response's headers
 */
namespace Opulence\Tests\HTTP\Middleware\Mocks;
use Closure;
use Opulence\HTTP\Middleware\IMiddleware;
use Opulence\HTTP\Requests\Request;
use Opulence\HTTP\Responses\Response;

class HeaderSetter implements IMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);
        $response->getHeaders()->add("foo", "bar");

        return $response;
    }
}