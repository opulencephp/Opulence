<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks middleware that returns something
 */
namespace RDev\Tests\HTTP\Routing\Mocks;
use RDev\HTTP\Middleware;
use RDev\HTTP\Requests;
use RDev\HTTP\Responses;
use RDev\HTTP\Routing\Routes;

class ReturnsSomethingMiddleware implements Middleware\IMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function handle(Requests\Request $request, \Closure $next)
    {
        /** @var Responses\Response $response */
        $response = $next($request);

        if($response !== null)
        {
            $response->setContent($response->getContent() . ":something");

            return $response;
        }
        else
        {
            return new Responses\RedirectResponse("/bar");
        }
    }
}