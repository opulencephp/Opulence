<?php
/**
 * Copyright (C) 2015 David Young
 *
 * Mocks middleware that returns something
 */
namespace RDev\Tests\Routing\Mocks;
use Closure;
use RDev\HTTP\Middleware\IMiddleware;
use RDev\HTTP\Requests\Request;
use RDev\HTTP\Responses\RedirectResponse;
use RDev\HTTP\Responses\Response;

class ReturnsSomethingMiddleware implements IMiddleware
{
    /**
     * {@inheritdoc}
     */
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if($response !== null)
        {
            $response->setContent($response->getContent() . ":something");

            return $response;
        }
        else
        {
            return new RedirectResponse("/bar");
        }
    }
}