<?php
/**
 * Copyright (C) 2015 David Young
 * 
 * Mocks a middleware that writes to the response's headers
 */
namespace RDev\Tests\HTTP\Middleware\Mocks;
use RDev\HTTP\Middleware;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;

class HeaderSetter implements Middleware\IMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function handle(Requests\Request $request, \Closure $next)
    {
        /** @var Responses\Response $response */
        $response = $next($request);
        $response->getHeaders()->add("foo", "bar");

        return $response;
    }
}